<?php

namespace nomit\Security\Authentication\Token\Storage;

use nomit\Cryptography\EncrypterInterface;
use nomit\Dumper\Dumper;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Security\Session\SessionInterface;
use nomit\Serialization\SerializerResolverInterface;

class SessionTokenStorage extends AbstractTokenStorage
{

    public function __construct(
        private SessionInterface $session,
        string $tokenName,
    )
    {
        parent::__construct($tokenName);
    }

    public function setSession(SessionInterface $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function setToken(?TokenInterface $token): TokenStorageInterface
    {
        parent::setToken($token);

        if($token === null) {
            $this->session->remove($this->tokenName);

            return $this;
        }

        $this->session->set($this->tokenName, $token);

        return $this;
    }

    public function hasToken(): bool
    {
        return $this->getSession()->has($this->tokenName);
    }

    public function getToken(): ?TokenInterface
    {
        parent::getToken();

        if($this->token) {
            return $this->token;
        }

        if(!$this->session->has($this->tokenName)) {
            return null;
        }

        return $this->session->get($this->tokenName);
    }

    public function removeToken(): void
    {
        $this->setToken(null);
    }

}