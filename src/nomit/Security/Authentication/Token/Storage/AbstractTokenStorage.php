<?php

namespace nomit\Security\Authentication\Token\Storage;

use nomit\Cryptography\EncrypterInterface;
use nomit\Dumper\Dumper;
use nomit\Exception\InvalidArgumentException;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Serialization\SerializerResolverInterface;

abstract class AbstractTokenStorage implements TokenStorageInterface
{

    protected ?TokenInterface $token = null;

    private $initializer;

    public function __construct(
        protected string $tokenName = 'security.authentication.token',
    )
    {
    }

    public function setInitializer(?callable $initializer): self
    {
        $this->initializer = $initializer;

        return $this;
    }

    public function getToken(): ?TokenInterface
    {
        if($initializer = $this->initializer) {
            $this->initializer = null;
            $initializer();
        }

        return $this->token;
    }

    public function setToken(?TokenInterface $token): TokenStorageInterface
    {
        if($token) {
            $this->getToken();
        }

        $this->initializer = null;
        $this->token = $token;

        return $this;
    }

}