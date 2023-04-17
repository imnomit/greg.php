<?php

namespace nomit\Security\Authentication\Authenticator;

use nomit\Dumper\Dumper;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Exception\LogicException;
use nomit\Security\Authentication\Event\AuthenticationEvents;
use nomit\Security\Authentication\Event\CheckPassportAuthenticationEvent;
use nomit\Security\Authentication\Event\FailedLoginAuthenticationEvent;
use nomit\Security\Authentication\Event\SuccessfulAuthenticationEvent;
use nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent;
use nomit\Security\Authentication\Event\TokenCreatedAuthenticationEvent;
use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\Authentication\Exception\BadCredentialsException;
use nomit\Security\Authentication\Exception\FailedLoginAuthenticationException;
use nomit\Security\Authentication\Exception\NotFoundUserException;
use nomit\Security\Authentication\Passport\Badge\UserBadge;
use nomit\Security\Authentication\Passport\PassportInterface;
use nomit\Security\Authentication\Passport\SelfValidatingPassport;
use nomit\Security\Authentication\Token\Storage\TokenStorageInterface;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Security\User\UserInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;
use Psr\Log\LoggerInterface;

class AuthenticatorResolver implements AuthenticatorResolverInterface, UserAuthenticatorInterface
{

    private const NAME_ATTRIBUTE_AUTHENTICATORS = '_security_authenticators';

    private const NAME_ATTRIBUTE_SKIPPED_AUTHENTICATORS = '_security_skipped_authenticators';

    public function __construct(
        private array $authenticators,
        private TokenStorageInterface $tokenStorage,
        private EventDispatcherInterface $eventDispatcher,
        private string $providerKey,
        private ?LoggerInterface $logger = null,
        private bool $eraseCredentials = true,
        private bool $hideUserNotFoundException = true,
        private array $requiredBadges = []
    )
    {
    }

    public function authenticateUser(UserInterface $user, AuthenticatorInterface $authenticator, RequestInterface $request, array $badges = []): ?ResponseInterface
    {
        $passport = new SelfValidatingPassport(
            new UserBadge(
                $user->getUserId(),
                function() use($user) {
                    return $user;
                },
            ),
            $badges
        );

        $token = $authenticator->tokenize($passport, $this->providerKey);
        $event = new TokenCreatedAuthenticationEvent($token, $passport);
        
        $this->eventDispatcher->dispatch($event);
        
        $token = $event->getToken();
        
        return $this->handleAuthenticationSuccess($token, $passport, $request, $authenticator);
    }
    
    public function supports(RequestInterface $request): ?bool
    {
        if (null !== $this->logger) {
            $context = ['provider_key' => $this->providerKey];

            if ($this->authenticators instanceof \Countable || \is_array($this->authenticators)) {
                $context['authenticators'] = \count($this->authenticators);
            }

            $this->logger->debug('Checking for authenticator support.', $context);
        }

        $authenticators = [];
        $skippedAuthenticators = [];
        $lazy = true;

        foreach ($this->authenticators as $authenticator) {
            if (null !== $this->logger) {
                $this->logger->debug('Checking support on authenticator.', [
                    'provider_key' => $this->providerKey,
                    'authenticator' => \get_class($authenticator)
                ]);
            }

            if (false !== $supports = $authenticator->supports($request)) {
                $authenticators[] = $authenticator;
                $lazy = $lazy && null === $supports;
            } else {
                if (null !== $this->logger) {
                    $this->logger->debug('Authenticator does not support the request.', [
                        'provider_key' => $this->providerKey,
                        'authenticator' => \get_class($authenticator)
                    ]);
                }

                $skippedAuthenticators[] = $authenticator;
            }
        }

        if (!$authenticators) {
            return false;
        }

        $request->attributes->set(self::NAME_ATTRIBUTE_AUTHENTICATORS, $authenticators);
        $request->attributes->set(self::NAME_ATTRIBUTE_SKIPPED_AUTHENTICATORS, $skippedAuthenticators);

        return $lazy ? null : true;
    }

    public function authenticate(RequestInterface $request, ResponseInterface $response = null): ?ResponseInterface
    {
        $authenticators = $request->attributes->get(self::NAME_ATTRIBUTE_AUTHENTICATORS);

        $request->attributes->remove(self::NAME_ATTRIBUTE_AUTHENTICATORS);
        $request->attributes->remove(self::NAME_ATTRIBUTE_SKIPPED_AUTHENTICATORS);

        if(!$authenticators) {
            return null;
        }

        return $this->executeAuthenticators($authenticators, $request);
    }

    private function executeAuthenticators(array $authenticators, RequestInterface $request): ?ResponseInterface
    {
        foreach($authenticators as $authenticator) {
            if(false === $authenticator->supports($request)) {
                $this->logger?->debug('Skipping the {authenticator} authenticator as it does not support the current request, [{path}.', ['authenticator' => get_class($authenticator), 'path' => $request->getPathInfo()]);

                continue;
            }

            try {
                $response = $this->executeAuthenticator($authenticator, $request);

                if(null !== $response) {
                    $this->logger?->debug('The {authenticator} authenticator has generated a response. Later authenticators will not be called.', ['authenticator' => get_class($authenticator)]);

                    return $response;
                }
            } finally {
                $this->logger?->debug('The {authenticator} authenticator has been executed.', [
                    'authenticator' => get_class($authenticator)
                ]);
            }
        }

        return null;
    }

    private function executeAuthenticator(AuthenticatorInterface $authenticator, RequestInterface $request): ?ResponseInterface
    {
        $passport = null;

        try {
            $passport = $authenticator->authenticate($request);
            $event = new CheckPassportAuthenticationEvent($authenticator, $passport);

            try {
                $this->eventDispatcher->dispatch($event);
            } catch(\Throwable $exception) {
                $this->logger?->error('An error, code {code}, occurred in {file} at line {line} while attempting to validate the authentication passport generated by the {authenticator} authenticator: {message}.', [
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'authenticator' => get_class($authenticator),
                    'message' => $exception->getMessage(),
                    'exception' => $exception
                ]);

                throw $exception;
            }

            $resolvedBadges = [];

            foreach($passport->getBadges() as $badge) {
                if(!$badge->isResolved()) {
                    throw new BadCredentialsException(sprintf('Authentication has failed, as the security badge "%s" is not resolved. Did you forget to register the correct event listeners?', get_debug_type($badge)));
                }

                $resolvedBadges[] = get_class($badge);
            }

            $missingRequiredBadges = array_diff($this->requiredBadges, $resolvedBadges);

            if($missingRequiredBadges) {
                throw new BadCredentialsException(sprintf('Authentication has failed, as some badges marked as required are not available on the passport: "%s".', implode(', ', $missingRequiredBadges)));
            }

            $token = $authenticator->tokenize($passport, $this->providerKey);
            $event = new TokenCreatedAuthenticationEvent($token, $passport);

            $this->eventDispatcher->dispatch($event);

            $token = $event->getToken();

            if($this->eraseCredentials) {
                $token->eraseCredentials();
            }

            try {
                $this->eventDispatcher->dispatch(
                    new SuccessfulAuthenticationEvent($token, $request->getClientIp()),
                    AuthenticationEvents::AUTHENTICATION_SUCCESS
                );
            } finally {
                $this->logger?->info('Authentication by the {authenticator} authenticator has been successful.', [
                    'token' => $token,
                    'authenticator' => get_class($authenticator)
                ]);
            }
        } catch(AuthenticationException $exception) {
            $response = $this->handleAuthenticationFailure($exception, $request, $authenticator, $passport);

            if($response instanceof ResponseInterface) {
                return $response;
            }

            throw $exception;
        }

        if(!isset($token)) {
            throw new LogicException('The "$authenticatedToken" variable has not been assigned a value as expected.');
        }

        $response = $this->handleAuthenticationSuccess($token, $passport, $request, $authenticator);

        if($response instanceof ResponseInterface) {
            return $response;
        }

        $this->logger->warning('The {authenticator} authenticator has no "on-success" response. As such, the request will continue.', [
            'authenticator' => get_class($authenticator)
        ]);

        return null;
    }

    private function handleAuthenticationSuccess(
        TokenInterface $token,
        PassportInterface $passport,
        RequestInterface $request,
        AuthenticatorInterface $authenticator
    ): ?ResponseInterface
    {
        $response = $authenticator->onSuccessfulAuthentication($request, $token, $this->providerKey);

        $event = new SuccessfulLoginAuthenticationEvent(
            $authenticator,
            $passport,
            $token,
            $request,
            $response,
            $this->providerKey,
        );

        $this->eventDispatcher?->dispatch($event);

        if(!$event->isAuthenticated()) {
            throw new FailedLoginAuthenticationException(sprintf('An error occurred while attempting to validate the generated authentication token, username "%s" and IP address "%s", with the configured token persistence service.', $token->getUsername(), $request->getClientIp()));
        }

        $this->tokenStorage->setToken($event->getToken());

        return $event->getResponse();
    }

    private function handleAuthenticationFailure(
        AuthenticationException $exception,
        RequestInterface $request,
        AuthenticatorInterface $authenticator,
        ?PassportInterface $passport
    ): ?ResponseInterface
    {
        $this->logger?->warning('Authentication by the {authenticator} authenticator has failed.', [
            'authenticator' => get_class($authenticator)
        ]);

        if($this->hideUserNotFoundException && ($exception instanceof NotFoundUserException)) {
            $exception = new BadCredentialsException('The supplied credentials are invalid.', 0, $exception);
        }

        $response = $authenticator->onFailedAuthentication($request, $exception);

        if(null !== $response && null !== $this->logger) {
            $this->logger->debug('The {authenticator} authenticator has returned a failure response.', [
                'authenticator' => get_class($authenticator)
            ]);
        }

        $event = new FailedLoginAuthenticationEvent(
            $exception,
            $authenticator,
            $request,
            $response,
            $this->providerKey,
            $passport
        );

        $this->eventDispatcher->dispatch($event);

        return $event->getResponse();
    }

}