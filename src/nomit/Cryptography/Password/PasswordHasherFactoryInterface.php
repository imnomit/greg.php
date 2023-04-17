<?php

namespace nomit\Cryptography\Password;

use nomit\Security\User\PasswordAuthenticatedUserInterface;

interface PasswordHasherFactoryInterface
{

    public function getPasswordHasher(string|PasswordAuthenticatedUserInterface|PasswordHasherAwareInterface $user): PasswordHasherInterface;

}