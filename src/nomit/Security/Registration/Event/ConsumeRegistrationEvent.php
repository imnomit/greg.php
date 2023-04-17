<?php

namespace nomit\Security\Registration\Event;

use nomit\EventDispatcher\Event;
use nomit\Security\Authentication\Passport\PassportInterface;
use nomit\Security\Registration\Token\TokenInterface;
use nomit\Web\Request\RequestInterface;

final class ConsumeRegistrationEvent extends Event
{

    public function __construct(
        private RequestInterface $request,
        private TokenInterface $token,
        private PassportInterface $passport
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
     * @param PassportInterface $passport
     */
    public function setPassport(PassportInterface $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * @return PassportInterface
     */
    public function getPassport(): PassportInterface
    {
        return $this->passport;
    }

}