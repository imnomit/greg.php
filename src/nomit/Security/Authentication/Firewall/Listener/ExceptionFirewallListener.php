<?php

namespace nomit\Security\Authentication\Firewall\Listener;

use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Exception\LogicException;
use nomit\Kernel\Event\ExceptionEvent;
use nomit\Kernel\Event\KernelEvents;
use nomit\Kernel\Exception\AccessDeniedHttpException;
use nomit\Kernel\Exception\HttpException;
use nomit\Security\Authentication\EntryPoint\EntryPointInterface;
use nomit\Security\Authentication\Exception\AccountException;
use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\Authentication\Exception\LogoutException;
use nomit\Security\Authentication\Exception\NotEntryPointException;
use nomit\Security\Authentication\Token\Storage\TokenStorageInterface;
use nomit\Security\Authentication\Trust\TrustResolverInterface;
use nomit\Security\Authentication\Utilities\TargetPathTrait;
use nomit\Security\Authentication\Utilities\WebUtilities;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\Response;
use nomit\Web\Response\ResponseInterface;
use Psr\Log\LoggerInterface;

class ExceptionFirewallListener
{

    use TargetPathTrait;

    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private TrustResolverInterface $trustResolver,
        private WebUtilities $webUtilities,
        private string $firewallName,
        private ?EntryPointInterface $entryPoint = null,
        private ?string $errorPage = null,
        private ?LoggerInterface $logger = null,
        private bool $stateless = false
    )
    {
    }

    public function register(EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->addListener(KernelEvents::EXCEPTION, [$this, 'onKernelException'], 1);
    }

    public function unregister(EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->removeListener(KernelEvents::EXCEPTION, [$this, 'onKernelException']);
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        do {
            if($exception instanceof AuthenticationException) {
                $this->handleAuthenticationException($event, $exception);

                return;
            }

            if($exception instanceof LogoutException) {
                $this->handleLogoutException($event, $exception);

                return;
            }
        } while(null !== $exception = $exception->getPrevious());
    }

    private function handleAuthenticationException(ExceptionEvent $event, AuthenticationException $exception): void
    {
        $this->logger?->error('An {type} exception has been thrown: the user is being redirected to the authentication entry point.', ['type' => AuthenticationException::class,'exception' => $exception]);

        try {
            $event->setResponse(
                $this->startAuthentication($event->getRequest(), $exception)
            );
            
            $event->allowCustomResponseCode();
        } catch(\Exception $exception) {
            $event->setThrowable($exception);
        }
    }

    private function handleLogoutException(ExceptionEvent $event, LogoutException $exception): void
    {
        $event->setThrowable(new AccessDeniedHttpException($exception->getMessage(), $exception));

        $this->logger?->error('A {type} exception was thrown. It is being wrapped with a {access_denied} exception.', [
            'type' => LogoutException::class,
            'access_denied' => AccessDeniedHttpException::class,
            'exception' => $exception
        ]);
    }

    private function startAuthentication(RequestInterface $request, AuthenticationException $exception): ResponseInterface
    {
        if(null === $this->entryPoint) {
            $this->throwUnauthorizedException($exception);
        }

        $this->logger?->debug('The configured authentication entry point, {entrypoint}, is being called.', ['entrypoint' => $this->entry_point::class]);

        if(!$this->stateless) {
            $this->setTargetPath($request);
        }

        if($exception instanceof AccountException) {
            $this->tokenStorage->setToken(null);

            $this->logger?->info('The security token was removed due to an {class} exception.', ['class' => $exception::class, 'exception' => $exception]);
        }

        try {
            $response = $this->entryPoint->respond($request, $exception);
        } catch(NotEntryPointException $newException) {
            $this->throwUnauthorizedException($exception);
        }

        if(!isset($response) || !$response instanceof Response) {
            throw new LogicException(sprintf('The "%s::start()" method must reuturn a "%s"-typed object: instead, "%s" was returned.', get_debug_type($this->entry_point), Response::class, get_debug_type($response)));
        }

        return $response;
    }

    private function setTargetPath(RequestInterface $request): void
    {
        if($request->hasSession() && $request->isMethodSafe() && !$request->isXmlHttpRequest()) {
            $this->saveTargetPath($request->getSession(), $this->firewall_name, $request->getUri());
        }
    }

    private function throwUnauthorizedException(AuthenticationException $exception): void
    {
        $this->logger?->notice(sprintf('No authentication entry point has been configured that returns a "%s" HTTP response. Configure "entry_point" on the firewall "%s" to modify the response.', Response::HTTP_UNAUTHORIZED, $this->firewall_name));

        throw new HttpException(Response::HTTP_UNAUTHORIZED, $exception->getMessage(), $exception, [], $exception->getCode());
    }

}