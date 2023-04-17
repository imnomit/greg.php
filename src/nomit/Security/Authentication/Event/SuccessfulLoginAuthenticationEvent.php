<?php

namespace nomit\Security\Authentication\Event;

use nomit\Security\Authentication\Authenticator\AuthenticatorInterface;
use nomit\Security\Authentication\Passport\PassportInterface;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Security\User\UserInterface;
use nomit\Web\Request\Request;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

class SuccessfulLoginAuthenticationEvent extends LoginAuthenticationEvent
{

    private bool $authenticated = false;

    public function __construct(
        private AuthenticatorInterface $authenticator,
        private PassportInterface $passport,
        private TokenInterface $token,
        private RequestInterface $request,
        private ?ResponseInterface $response,
        private string $providerKey,
    )
    {
    }

    /**
     * @return AuthenticatorInterface
     */
    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticator;
    }

    /**
     * @return PassportInterface
     */
    public function getPassport(): PassportInterface
    {
        return $this->passport;
    }

    public function getUser(): UserInterface
    {
        return $this->passport->getUser();
    }

    /**
     * @param TokenInterface $token
     */
    public function setToken(TokenInterface $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return TokenInterface
     */
    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getProviderKey(): string
    {
        return $this->providerKey;
    }

    /**
     * @param ResponseInterface|null $response
     */
    public function setResponse(?ResponseInterface $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param bool $authenticated
     */
    public function setAuthenticated(bool $authenticated): self
    {
        $this->authenticated = $authenticated;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

}