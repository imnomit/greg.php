<?php

namespace nomit\Security\Authentication\Event;

use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Web\Request\RequestInterface;

class DeAuthenticatedTokenAuthenticationEvent extends AuthenticationEvent
{

    public function __construct(
        private TokenInterface $token,
        private RequestInterface $request
    )
    {
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

}