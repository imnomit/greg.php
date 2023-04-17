<?php

namespace nomit\Security\Registration\Event;

use nomit\Cryptography\Password\PasswordHasherInterface;
use nomit\EventDispatcher\Event;
use nomit\Security\Registration\RegistrationInterface;

final class HashingPasswordRegistrationEvent extends Event
{

    public function __construct(
        private RegistrationInterface $registration,
        private PasswordHasherInterface $hasher,
        private string $password
    )
    {
    }

    public function getRegistration(): RegistrationInterface
    {
        return $this->registration;
    }

    /**
     * @return PasswordHasherInterface
     */
    public function getHasher(): PasswordHasherInterface
    {
        return $this->hasher;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function hash(string $password): string
    {
        return $this->hasher->hash($password);
    }

    public function needsRehashing(string $hash): bool
    {
        return $this->hasher->needsRehash($hash);
    }

}