<?php

namespace nomit\Security\Authentication\Token;

use nomit\Exception\InvalidArgumentException;
use nomit\Security\User\UserInterface;

class PreAuthenticationToken extends AbstractToken
{

    public function __construct(
        UserInterface $user,
        private mixed $credentials,
        private string $providerKey
    )
    {
        parent::__construct($user);


        if(empty($providerKey)) {
            throw new InvalidArgumentException('The "$providerKey" argument must not be empty.');
        }

        if(count($this->getRoles()) > 0) {
            $this->authenticate(true);
        }
    }

    /**
     * @return string
     */
    public function getProviderKey(): string
    {
        return $this->providerKey;
    }

    public function getCredentials(): mixed
    {
        return $this->credentials;
    }

    public function eraseCredentials(): void
    {
        parent::eraseCredentials();

        $this->credentials = null;
    }

}