<?php

namespace nomit\Security\Authentication\User;

use nomit\Security\User\UserInterface;

interface PasswordUpgradingUserInterface
{

    public function upgradePassword(UserInterface $user, string $encodedPassword): void;

}