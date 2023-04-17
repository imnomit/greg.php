<?php

namespace nomit\Security\Authentication\User;

use nomit\Security\Authentication\Exception\BannedUserException;
use nomit\Security\Authentication\Exception\ExpiredAccountException;
use nomit\Security\Authentication\Exception\ExpiredCredentialsAccountException;
use nomit\Security\Authentication\Exception\LockedAccountException;
use nomit\Security\Authentication\Exception\ValidateUserException;
use nomit\Security\Authentication\Passport\PassportInterface;
use nomit\Security\User\Authentication\Validation\ValidatorInterface;
use nomit\Security\User\ExtendedUserInterface;
use nomit\Security\User\User;
use nomit\Security\User\UserInterface;

class UserValidator implements UserValidatorInterface
{

    /**
     * UserValidator constructor.
     * @param ValidatorInterface[] $validators
     */
    public function __construct(
        private array $validators = []
    )
    {
    }

    public function addValidator(ValidatorInterface $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param UserInterface $user
     * @return bool
     * @throws \Exception
     */
    public function checkPreAuthentication(UserInterface $user): bool
    {
        if(!$user instanceof ExtendedUserInterface && !$user instanceof User) {
            return true;
        }

        foreach($this->validators as $validator) {
            if(!$validator->validate($user)) {
                throw new ValidateUserException($user, sprintf($validator->getMessage(), $user->getUsername()));
            }
        }

        if($user->isLocked()) {
            throw new LockedAccountException($user);
        }

        if($user->isExpired()) {
            throw new ExpiredAccountException($user);
        }

        return true;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function checkPostAuthentication(UserInterface $user): bool
    {
        if(!$user instanceof ExtendedUserInterface && !$user instanceof User) {
            return true;
        }

        if($user->isBanned()) {
            $ban = $user->getBan();

            throw new BannedUserException($user, sprintf('The user with the username "%s" is banned until "%s": as a result, he/she is unable to sign into the site until the ban has expired.', $user->getUsername(), $ban->getDateTime()->format('c')));
        }

        if($user->isCredentialsExpired()) {
            throw new ExpiredCredentialsAccountException($user);
        }

        return true;
    }

}