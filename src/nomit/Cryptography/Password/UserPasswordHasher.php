<?php

namespace nomit\Cryptography\Password;

use nomit\Security\User\PasswordAuthenticatedUserInterface;

class UserPasswordHasher implements UserPasswordHasherInterface
{

    /**
     * @var PasswordHasherFactoryInterface
     */
    private PasswordHasherFactoryInterface $hasher_factory;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasher_factory = $hasherFactory;
    }

    public function hashPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword): string
    {
        $salt = $user->getSalt();

        $hasher = $this->hasher_factory->getPasswordHasher($user);

        return $hasher->hash($plainPassword, $salt);
    }

    public function isPasswordValid(PasswordAuthenticatedUserInterface $user, string $plainPassword): bool
    {
        $salt = $user->getSalt();

        if (null === $user->getPassword()) {
            return false;
        }

        $hasher = $this->hasher_factory->getPasswordHasher($user);

        return $hasher->verify($user->getPassword(), $plainPassword, $salt);
    }

    public function needsRehash(PasswordAuthenticatedUserInterface $user): bool
    {
        if (null === $user->getPassword()) {
            return false;
        }

        $hasher = $this->hasher_factory->getPasswordHasher($user);

        return $hasher->needsRehash($user->getPassword());
    }

}