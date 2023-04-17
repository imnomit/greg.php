<?php

namespace Application\Security\Authentication\EventListener;

use Application\EventListener\AbstractRoutingEventListener;
use nomit\Dumper\Dumper;
use nomit\Error\Frame\Inspector;
use nomit\EventDispatcher\EventSubscriberInterface;
use nomit\Exception\ExceptionInterface;
use nomit\Exception\InvalidArgumentException;
use nomit\Exception\LogicException;
use nomit\Kernel\Event\KernelEvents;
use nomit\Kernel\Event\RequestEvent;
use nomit\Routing\Exception\PageNotFoundException;
use nomit\Routing\RouteCollector;
use nomit\Routing\RouteInterface;
use nomit\Security\User\UserInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Routing\Router;
use nomit\Security\Authentication\Exception\NotFoundUserException;
use nomit\Security\Authentication\Token\Storage\TokenStorageInterface;
use nomit\Security\Authentication\User\UserProviderInterface;
use nomit\Security\Authentication\Utilities\AuthenticationUtilities;
use nomit\Security\Csrf\TokenManagerInterface;
use nomit\Web\Response\JsonResponse;
use nomit\Web\Response\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class RoutingAuthenticationEventListener extends AbstractRoutingEventListener
{

    public function __construct(
        ContainerInterface $container,
        private TokenManagerInterface $tokenManager,
        private TokenStorageInterface $tokenStorage,
        private UserProviderInterface $userProvider,
        private AuthenticationUtilities $utilities,
        array $options = [],
        ?LoggerInterface $logger = null,
    )
    {
        parent::__construct($container, $options, $logger);
    }

    public function handle(\Psr\Http\Message\RequestInterface $request): ResponseInterface
    {
        $route = $request->attributes->get($this->getAttributeName());

        try {
            match($route->getName()) {
                'login' => $response = $this->getLoginToken($request),
                'register' => $response = $this->getRegistrationToken($request),
                'authentication_data' => $response = $this->getAuthenticationData($request),
                'verify_user' => $response = $this->verifyUser($request),
                'authentication_success' => $response = $this->handleSuccessfulAuthentication($request),
                'authentication_failure' => $response = $this->handleFailedAuthentication($request),
                default => throw new InvalidArgumentException(sprintf('The matched controller, named "%s", does not correspond with any of the configured routes.', $route->getName()))
            };
        } catch(ExceptionInterface $exception) {
            $this->logger?->error('An unexpected error occurred while attempting to dispatch the handled request to the matched route: {message}.', [
                'exception' => $exception,
                'message' => $exception->getMessage(),
                'request' => $request->toArray()
            ]);

            throw $exception;
        }

        if(!isset($response) || !$response instanceof ResponseInterface) {
            throw new LogicException(sprintf('A "%s"-typed object is expected to be returned by the matched authentication resource. Instead, a "%s"-typed value was returned.', ResponseInterface::class, get_debug_type($response)));
        }

        return $response;
    }

    public function getLoginToken(RequestInterface $request): ResponseInterface
    {
        $token = $this->tokenManager->getToken($this->options['login_csrf_token_parameter']);

        return new JsonResponse([
            'token' => $token->getValue()
        ], null, 'login');
    }

    public function getRegistrationToken(RequestInterface $request): ResponseInterface
    {
        $token = $this->tokenManager->getToken($this->options['registration_csrf_token_parameter']);

        return new JsonResponse([
            'token' => $token->getValue()
        ], null, 'register');
    }

    public function getAuthenticationData(RequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'last_username' => $this->utilities->getLastUsername(),
            'error' => $this->utilities->getLastAuthenticationError()
        ], null, 'authentication');
    }

    public function verifyUser(RequestInterface $request): ResponseInterface
    {
        $username = $request->get('username');

        if(!$username) {
            throw new InvalidArgumentException('The "username" parameter is missing from the handled request.');
        }

        return new JsonResponse([
            'user' => $this->getUser($username)
        ], null, 'user');
    }

    public function handleSuccessfulAuthentication(RequestInterface $request): ResponseInterface
    {
        $lastUsername = $this->utilities->getLastUsername();

        if (!$lastUsername) {
            throw new LogicException('No last username has been assigned to the session of the handled request, and so no user data can be returned.');
        }

        $user = $this->getUser($lastUsername, false);

        return new JsonResponse([
            'success' => true,
            'user' => $user->toArray(),
            'permissions' => $user->getPermissions()
        ], null, 'success');
    }

    public function handleFailedAuthentication(RequestInterface $request): ResponseInterface
    {
        $exception = $this->utilities->getLastAuthenticationError();

        if(!$exception) {
            throw new LogicException('No last error has been assigned to the session of the handled request, and so no error data can be returned.');
        }

        throw $exception;
    }

    private function getUser(string $username, bool $toArray = true): array|UserInterface
    {
        if(!$user = $this->userProvider->getByUsername($username)) {
            throw new NotFoundUserException(sprintf('No user with the username "%s" could be found.', $username));
        }

        if($toArray) {
            $user = $user->toArray();

            unset($user['password']);
        }

        return $user;
    }

    public static function getDispatcherName(): ?string
    {
        return null;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

}