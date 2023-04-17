<?php

namespace nomit\Security\Authentication\Exception;

use nomit\Security\User\UserInterface;

class UserException extends AuthenticationException
{

    public function __construct(
        private UserInterface $user,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function __serialize(): array
    {
        return [$this->user, parent::__serialize()];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->user,
            $parentData
        ] = $data;

        parent::__unserialize($parentData);
    }

}