<?php

namespace nomit\Security\Authentication\Passport\Badge\Credentials;

use nomit\Exception\LogicException;

class Credentials implements CredentialsInterface
{

    private bool $resolved = false;

    public function __construct(
        private ?string $password
    )
    {
    }

    public function getPassword(): string
    {
        if(null === $this->password) {
            throw new LogicException(sprintf('The "%s" credentials cannot be read, as they have already been erased by another listener.', __CLASS__));
        }

        return $this->password;
    }

    public function resolve(): void
    {
        $this->resolved = true;
        $this->password = null;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

}