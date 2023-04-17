<?php

namespace nomit\Security\Registration\Exception;

use nomit\Security\Registration\RegistrationInterface;
use Throwable;

class NoResponseRegistrationException extends RegistrationException
{

    public function __construct(RegistrationInterface $registration)
    {
        parent::__construct(
            $registration,
            sprintf('The registration of the user named "%s" and with a username of "%s" failed, as no response was received by the "%s" event listener.', $registration->getName(), $registration->getUsername(), RegistrationEventListener::class)
        );
    }

}