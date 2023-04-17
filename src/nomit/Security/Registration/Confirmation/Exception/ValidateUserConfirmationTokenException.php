<?php

namespace nomit\Security\Registration\Confirmation\Exception;

use nomit\Security\Registration\Confirmation\Token\TokenInterface;
use Throwable;

class ValidateUserConfirmationTokenException extends ConfirmationTokenException
{

    public function __construct(TokenInterface $token)
    {
        parent::__construct(
            $token,
            sprintf('An error occurred while attempting to mark as validated the user with the user ID "%s" via the confirmation token with the token ID "%s".', $token->getUserId(), $token->getTokenId())
        );
    }

}