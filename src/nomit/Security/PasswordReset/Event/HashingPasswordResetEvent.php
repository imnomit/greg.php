<?php

namespace nomit\Security\PasswordReset\Event;

use nomit\Cryptography\Password\PasswordHasherInterface;
use nomit\Security\User\UserInterface;

final class HashingPasswordResetEvent extends PasswordResetEvent
{

    public function __construct(
        private UserInterface $user,
        private PasswordHasherInterface $hasher,
        private string $password
    )
    {
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return PasswordHasherInterface
     */
    public function getHasher(): PasswordHasherInterface
    {
        return $this->hasher;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
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