<?php

namespace nomit\Security\Authentication\RememberMe;

use nomit\Exception\InvalidArgumentException;
use nomit\Exception\RuntimeException;
use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\Authentication\Exception\CookieTheftRememberMeAuthenticationException;
use nomit\Security\Authentication\Exception\NotFoundUserException;
use nomit\Security\Authentication\Exception\UnsupportedUserException;
use nomit\Security\Authentication\Token\RememberMeToken;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Security\Authentication\User\UserProviderInterface;
use nomit\Security\User\UserInterface;
use nomit\Web\Cookie;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractRememberMeProvider implements RememberMeProviderInterface
{

    public const COOKIE_DELIMITER = '::';

    private array $defaultOptions = [
        'secure' => false,
        'http_only' => true,
        'same_site' => null,
    ];

    protected array $options = [];

    public function __construct(
        private array $userProviders,
        private string $secret,
        private string $providerKey,
        array $options = [],
        private ?LoggerInterface $logger = null
    )
    {
        $this->setOptions($options);
    }

    public function setOptions(array $options = []): self
    {
        $this->options = array_merge($this->defaultOptions, $options);

        return $this;
    }

    public function getRememberMeParameter(): string
    {
        return $this->options['remember_me_parameter'];
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function autoLogin(RequestInterface $request): ?TokenInterface
    {
        if(($cookie = $request->attributes->get(self::COOKIE_ATTRIBUTE_NAME)) && null === $cookie->getValue()) {
            return null;
        }

        if(null === $cookie = $request->cookies->get($this->options['name'])) {
            return null;
        }

        if(null !== $this->logger) {
            $this->logger->debug(sprintf('A "remember-me" cookie has been detected by "%s::%s()".', __CLASS__, __METHOD__));
        }

        $cookieParts = $this->decodeCookie($cookie);

        try {
            $user = $this->processAutoLoginCookie($cookieParts, $request);

            if(!$user instanceof UserInterface) {
                throw new RuntimeException(sprintf('The "%s"::processAutoLoginCookie()" method must return a "UserInterface" implementation.', __CLASS__));
            }

            if(null !== $this->logger) {
                $this->logger->info('The detected "remember-me" cookie has been accepted.');
            }

            return new RememberMeToken($user, $this->providerKey, $this->secret);
        } catch(CookieTheftRememberMeAuthenticationException $exception) {
            $this->failedLogin($request, $exception);

            throw $exception;
        } catch(NotFoundUserException $exception) {
            if(null !== $this->logger) {
                $this->logger->error('No user with the current cookie\'s username could be found.', ['exception' => $exception]);
            }

            $this->failedLogin($request, $exception);
        } catch(UnsupportedUserException $exception) {
            if(null !== $this->logger) {
                $this->logger->warning('The user associated with the current cookie is of an unsupported class.', ['exception' => $exception]);
            }

            $this->failedLogin($request, $exception);
        } catch(AuthenticationException $exception) {
            if(null !== $this->logger) {
                $this->logger->warning('One or more errors occurred during the "remember-me" authentication process, causing it to fail.', ['exception' => $exception]);
            }

            $this->failedLogin($request, $exception);
        } catch(\Throwable $exception) {
            $this->failedLogin($request, $exception);

            throw $exception;
        }

        return null;
    }

    public function logout(RequestInterface $request, ResponseInterface $response, TokenInterface $token): void
    {
        $this->removeCookie($request);
    }

    public function failedLogin(RequestInterface $request, \Exception $exception = null)
    {
        $this->removeCookie($request);
        $this->onFailedLogin($request, $exception);
    }

    final public function successfulLogin(RequestInterface $request, ResponseInterface $response, TokenInterface $token)
    {
        $this->removeCookie($request);

        if(!$token->getUser() instanceof UserInterface) {
            if(null !== $this->logger) {
                $this->logger->warning('The current "remember-me" token will be ignored, as it does not contain a "UserInterface" implementation.', ['token' => $token]);
            }

            return;
        }


        if(!$this->isRememberMeRequested($request)) {
            if(null !== $this->logger) {
                $this->logger->warning('The "remember-me" service was not requested by the current request.');
            }

            return;
        }

        if(null !== $this->logger) {
            $this->logger->debug('The "remember-me" service was requested by the current request, and a cookie is being set.');
        }

        $request->attributes->remove(self::COOKIE_ATTRIBUTE_NAME);

        $this->onLoginSuccess($request, $response, $token);
    }

    protected function onFailedLogin(RequestInterface $request, \Throwable $exception = null): void
    {

    }

    final protected function getUserProvider(string $className): UserProviderInterface
    {
        foreach($this->userProviders as $provider) {
            if($provider->supportsClass($className)) {
                return $provider;
            }
        }

        throw new UnsupportedUserException($className);
    }

    protected function decodeCookie(string $payload): array
    {
        return explode(self::COOKIE_DELIMITER, base64_decode($payload));
    }

    protected function encodeCookie(array $parts): string
    {
        foreach($parts as $part) {
            if(str_contains($part, self::COOKIE_DELIMITER)) {
                throw new InvalidArgumentException('No "$part" item should contain the cookie delimiter, "%s".', self::COOKIE_DELIMITER);
            }
        }

        return base64_encode(implode(self::COOKIE_DELIMITER, $parts));
    }

    protected function removeCookie(RequestInterface $request): void
    {
        if(null !== $this->logger) {
            $this->logger->debug('The current "remember-me" cookie is being removed.', ['name' => $this->options['name']]);
        }

        $request->attributes->set(self::COOKIE_ATTRIBUTE_NAME, new Cookie(
            $this->options['name'],
            null,
            1,
            $this->options['path'],
            $this->options['domain'],
            $this->options['secure'] ?? $request->isSecure(),
            $this->options['http_only'],
            false,
            $this->options['same_site']
        ));
    }

    protected function isRememberMeRequested(RequestInterface $request): bool
    {
        if(true === $this->options['always_remember_me']) {
            return true;
        }

        $parameter = $request->get($this->options['remember_me_parameter']);

        if(null === $parameter && null !== $this->logger) {
            $this->logger->debug('No "remember-me" cookie was received with the current request.', ['parameter' => $this->options['remember_me_parameter']]);
        }

        return 'true' === $parameter || 'on' === $parameter || '1' === $parameter || 'yes' === $parameter || true === $parameter;
    }

    abstract protected function processAutoLoginCookie(array $parts, RequestInterface $request): UserInterface;

    abstract protected function onLoginSuccess(RequestInterface $request, ResponseInterface $response, TokenInterface $token);

}