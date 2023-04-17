<?php

namespace nomit\Security\Authentication\RememberMe;

use nomit\Cryptography\Entropy\EntropyFactory;
use nomit\Cryptography\Entropy\Generator;
use nomit\Cryptography\Security\Strength;
use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\Authentication\Exception\CookieTheftRememberMeAuthenticationException;
use nomit\Security\Authentication\RememberMe\Token\RememberMeToken;
use nomit\Security\Authentication\RememberMe\Token\Storage\RememberMeTokenStorageInterface;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Security\User\UserInterface;
use nomit\Web\Cookie;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

class PersistentTokenRememberMeProvider extends AbstractRememberMeProvider
{

    private RememberMeTokenStorageInterface $tokenStorage;

    public function setTokenStorage(RememberMeTokenStorageInterface $tokenStorage): self
    {
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    /**
     * @return RememberMeTokenStorageInterface
     */
    public function getTokenStorage(): RememberMeTokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function removeCookie(RequestInterface $request): void
    {
        parent::removeCookie($request);

        if(null !== ($cookie = $request->request->get($this->options['name']))
            && 2 === count($parts = $this->decodeCookie($cookie))
        ) {
            [$name] = $parts;

            $this->tokenStorage->deleteToken($name);
        }
    }

    protected function processAutoLoginCookie(array $parts, RequestInterface $request): UserInterface
    {
        if(2 !== count($parts)) {
            throw new AuthenticationException('The supplied "remember-me" cookie is invalid, as it has an improper number of delimited parts.');
        }

        [$series, $tokenValue] = $parts;

        $persistentToken = $this->tokenStorage->loadToken($series);

        if(!hash_equals($persistentToken->getTokenValue(), $tokenValue)) {
            throw new CookieTheftRememberMeAuthenticationException('This token has already been used. The user\'s account may be compromised.');
        }

        if($persistentToken->getLastUsedDateTime()->getTimestamp() + $this->options['lifetime'] < time()) {
            throw new AuthenticationException('The current "remember-me" cookie has expired.');
        }

        $generator = $this->getEntropyGenerator();
        $tokenValue = base64_encode($generator->generate(64));

        $this->tokenStorage->updateToken($series, $tokenValue, new \DateTime('NOW'));

        $request->attributes->set(self::COOKIE_ATTRIBUTE_NAME, new Cookie(
            $this->options['name'],
            $this->encodeCookie([$series, $tokenValue]),
            time() + $this->options['lifetime'],
            $this->options['path'],
            $this->options['domain'],
            $this->options['secure'] ?? $request->isSecure(),
            $this->options['http_only'],
            false,
            $this->options['same_site']
        ));

        return $this->getUserProvider($persistentToken->getClass())->loadUserByUsername($persistentToken->getUsername());
    }

    protected function onLoginSuccess(RequestInterface $request, ResponseInterface $response, TokenInterface $token)
    {
        $generator = $this->getEntropyGenerator();
        $name = base64_encode($generator->generate(64));
        $tokenValue = base64_encode($generator->generate(64));

        $this->tokenStorage->createToken(
            new RememberMeToken(
                get_class($user = $token->getUser()),
                $user->getUsername(),
                $name,
                $tokenValue,
                new \DateTime('NOW')
            )
        );

        $response->headers->setCookie(
            new Cookie(
                $this->options['name'],
                $this->encodeCookie([$name, $tokenValue]),
                time() + $this->options['lifetime'],
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'] ?? $request->isSecure(),
                $this->options['httponly'],
                false,
                $this->options['samesite']
            )
        );
    }

    private function getEntropyGenerator(): Generator
    {
        $factory = new EntropyFactory();

        return $factory->getGenerator(new Strength(Strength::MEDIUM));
    }

}