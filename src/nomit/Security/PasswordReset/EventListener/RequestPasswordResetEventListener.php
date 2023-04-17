<?php

namespace nomit\Security\PasswordReset\EventListener;

use nomit\Database\DateTime;
use nomit\DependencyInjection\Container;
use nomit\Dumper\Dumper;
use nomit\EventDispatcher\EventSubscriberInterface;
use nomit\Kernel\Event\KernelEvents;
use nomit\Kernel\Event\RequestEvent;
use nomit\Security\Authentication\Exception\NotFoundUserException;
use nomit\Security\Authentication\User\UserProviderInterface;
use nomit\Security\PasswordReset\Exception\ExceptionInterface;
use nomit\Security\PasswordReset\Exception\SaveTokenPasswordResetException;
use nomit\Security\PasswordReset\Mail\MailerInterface;
use nomit\Security\PasswordReset\Token\TokenInterface;
use nomit\Security\PasswordReset\Token\TokenManagerInterface;
use nomit\Security\User\UserInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Request\RequestMatcher;
use nomit\Web\Request\RequestMatcherInterface;
use nomit\Web\Response\JsonResponse;
use nomit\Web\Response\ResponseInterface;
use Psr\Log\LoggerInterface;

final class RequestPasswordResetEventListener implements EventSubscriberInterface
{

    private \SplObjectStorage $requests;

    public function __construct(
        private TokenManagerInterface $tokenManager,
        private MailerInterface $mailer,
        private \stdClass $options,
        private array $userProviders,
        private Container $container,
        private ?LoggerInterface $logger = null,
    )
    {
        $this->requests = new \SplObjectStorage();
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if(!$event->isMainRequest()) {
            $this->logger?->debug('The handled request object is not the main request object and so is being skipped.');

            return;
        }

        $request = $event->getRequest();

        if($this->requests->contains($request) && ($response = $this->requests->offsetGet($request)) instanceof ResponseInterface) {
            $event->setResponse($response);

            return;
        }

        if(!$this->createRequestMatcher()->matches($request)) {
            $this->logger?->debug('The handled request does not match the configured request-password reset path, {path}.', [
                'path' => $this->options->request->path,
                'request' => $request->toArray()
            ]);

            return;
        }

        try {
            $response = $this->handle($request);
        } catch(ExceptionInterface $exception) {
            $response = $this->onFailedPasswordReset($exception);
        } finally {
            $response = $response ?? null;

            $this->requests->attach($request, $response);

            if($response instanceof ResponseInterface) {
                $event->setResponse($response);
            } else {
                $this->logger?->warning('The value returned by either the success or failure handler was not the expected {response} value, but instead a {type} typed value.', [
                    'response' => ResponseInterface::class,
                    'type' => get_debug_type($response),
                    'request' => $request->toArray(),
                ]);
            }
        }
    }

    private function handle(RequestInterface $request): ?ResponseInterface
    {
        $email = $request->get('email');

        if(!$email) {
            $this->logger?->warning('The handled request object is missing the necessary "email" parameter.', [
                'request' => $request->toArray()
            ]);

            return null;
        }

        $user = $this->getUserProvider()->getByEmail($email);

        if(!$user) {
            throw new NotFoundUserException($email);
        }

        $tokenValue = $this->tokenManager->hash($user);

        if($hasToken = $this->tokenManager->hasToken($tokenValue)) {
            $this->logger?->warning('The user for which the password reset was requested, email {email}, already has an active password reset request pending.', [
                'email' => $email,
                'user' => $user->toArray()
            ]);

            return null;
        }

        if($hasToken && $this->tokenManager->getToken($tokenValue)->getExpiryDateTime() > new DateTime('NOW')) {
            $this->tokenManager->removeToken($tokenValue);
        }

        $token = $this->tokenManager->generateToken($user);

        If(!$this->tokenManager->saveToken($token)) {
            throw new SaveTokenPasswordResetException(sprintf('An error occurred while attempting to save the token with the token value "%s" for the user with the email "%s".', $token->getToken(), $user->getEmail()));
        }

        $this->mailer->setToken($token);

        try {
            $this->mailer->send($email, $this->options->request->mail->from, $this->options->request->mail->subject);
        } catch(\nomit\Mail\Exception\ExceptionInterface $exception) {
            return $this->onFailedMailerSend($exception);
        }

        return $this->onSuccessfulPasswordReset($token, $user);
    }

    private function onSuccessfulPasswordReset(TokenInterface $token, UserInterface $user): ResponseInterface
    {
        return new JsonResponse([
            'success' => true,
            'token' => $token->toArray(),
            'user' => $user->toArray()
        ]);
    }

    private function onFailedPasswordReset(ExceptionInterface $exception): ResponseInterface
    {
        if(!$this->options->catch_exceptions) {
            throw $exception;
        }

        return new JsonResponse([
            'success' => false,
            'exception' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode()
            ]
        ]);
    }

    private function onFailedMailerSend(\nomit\Mail\Exception\ExceptionInterface $exception): ResponseInterface {
        if(!$this->options->catch_exception) {
            throw $exception;
        }

        return new JsonResponse([
            'success' => false,
            'exception' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode()
            ]
        ]);
    }

    private function createRequestMatcher(): RequestMatcherInterface
    {
        return new RequestMatcher($this->options->request->path);
    }

    private function getUserProvider(): UserProviderInterface
    {
        return $this->container->get(current($this->userProviders));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public static function getDispatcherName(): ?string
    {
        return null;
    }

}