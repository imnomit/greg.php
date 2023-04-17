<?php

namespace nomit\Security\Registration\Exception;

use nomit\Security\Registration\RegistrationInterface;
use Throwable;

class EmailTakenRegistrationException extends RegistrationException
{

    public function __construct(RegistrationInterface $registration, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            $registration,
            sprintf('The supplied user email address, "%s", is already in use by another user.', $registration->getEmail())
        );
    }

}