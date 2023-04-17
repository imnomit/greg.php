<?php

namespace nomit\Cryptography\Password;

interface PasswordHasherInterface
{

    public const MAXIUM_PASSWORD_LENGTH = 4096;

    public function hash(string $plainPassword): string;

    public function verify(string $hashedPassword, string $plainPassword): bool;

    public function needsRehash(string $hashedPassword): bool;

}