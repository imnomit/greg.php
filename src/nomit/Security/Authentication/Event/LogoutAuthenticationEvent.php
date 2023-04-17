<?php

namespace nomit\Security\Authentication\Event;

use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

class LogoutAuthenticationEvent extends AuthenticationEvent
{

    private bool $deauthenticated = false;

    private ?ResponseInterface $response = null;

    public function __construct(
        private RequestInterface $request,
        private ?TokenInterface $token
    )
    {
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return TokenInterface|null
     */
    public function getToken(): ?TokenInterface
    {
        return $this->token;
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
     * @return bool
     */
    public function isDeauthenticated(): bool
    {
        return $this->deauthenticated;
    }

    /**
     * @param bool $deauthenticated
     */
    public function setDeauthenticated(bool $deauthenticated): self
    {
        $this->deauthenticated = $deauthenticated;

        return $this;
    }

}