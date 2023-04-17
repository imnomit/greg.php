<?php

namespace nomit\Security\Authentication\RememberMe;

use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\User\UserInterface;
use nomit\Web\Cookie;
use nomit\Web\Request\RequestInterface;

class TokenRememberMeProvider extends AbstractRememberMeProvider
{

    protected function processAutoLoginCookie(array $parts, RequestInterface $request): UserInterface
    {
        if (4 !== \count($parts)) {
            throw new AuthenticationException('The cookie is invalid.');
        }

        [$class, $username, $expires, $hash] = $parts;

        if (false === $username = base64_decode($username, true)) {
            throw new AuthenticationException('$username contains a character from outside the base64 alphabet.');
        }
        try {
            $user = $this->getUserProvider($class)->getByUsername($username);
        } catch (\Exception $e) {
            if (!$e instanceof AuthenticationException) {
                $e = new AuthenticationException($e->getMessage(), $e->getCode(), $e);
            }

            throw $e;
        }

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException(sprintf('The UserProviderInterface implementation must return an instance of UserInterface, but returned "%s".', \get_class($user)));
        }

        if (true !== hash_equals($this->generateCookieHash($class, $username, $expires, $user->getPassword()), $hash)) {
            throw new AuthenticationException('The cookie\'s hash is invalid.');
        }

        if ($expires < time()) {
            throw new AuthenticationException('The cookie has expired.');
        }

        return $user;
    }

    protected function onLoginSuccess(RequestInterface $request, ResponseInterface $response, TokenInterface $token)
    {
        $user = $token->getUser();
        $expires = time() + $this->options['lifetime'];
        $value = $this->generateCookieValue(\get_class($user), $user->getUsername(), $expires, $user->getPassword());

        $response->headers->setCookie(
            new Cookie(
                $this->options['name'],
                $value,
                $expires,
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'] ?? $request->isSecure(),
                $this->options['httponly'],
                false,
                $this->options['samesite']
            )
        );
    }

    private function generateCookieValue(string $class, string $username, int $expires, ?string $password): string
    {
        return $this->encodeCookie([
            $class,
            base64_encode($username),
            $expires,
            $this->generateCookieHash($class, $username, $expires, $password),
        ]);
    }

    private function generateCookieHash(string $class, string $username, int $expires, ?string $password): string
    {
        return hash_hmac('sha256', $class . self::COOKIE_DELIMITER . $username . self::COOKIE_DELIMITER.$expires . self::COOKIE_DELIMITER.$password, $this->getSecret());
    }

}