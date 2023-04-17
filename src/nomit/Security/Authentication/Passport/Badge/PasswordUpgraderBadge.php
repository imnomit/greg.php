<?php

namespace nomit\Security\Authentication\Passport\Badge;

use nomit\Exception\LogicException;
use nomit\Security\Authentication\User\PasswordUpgradingUserInterface;

class PasswordUpgraderBadge implements BadgeInterface
{

    public function __construct(
        private ?string $plaintextPassword,
        private ?PasswordUpgradingUserInterface $passwordUpgrader = null
    )
    {
    }

    public function getAndErasePlaintextPassword(): string
    {
        $password = $this->plaintextPassword;

        if(null === $password) {
            throw new LogicException('The current badge\'s password has already been erased by another listener.');
        }

        $this->plaintextPassword = null;

        return $password;
    }

    /**
     * @return PasswordUpgradingUserInterface|null
     */
    public function getPasswordUpgrader(): ?PasswordUpgradingUserInterface
    {
        return $this->passwordUpgrader;
    }

    public function isResolved(): bool
    {
        return true;
    }

}