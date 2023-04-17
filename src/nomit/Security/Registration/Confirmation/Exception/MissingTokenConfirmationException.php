<?php

namespace nomit\Security\Registration\Confirmation\Exception;

use nomit\Security\Registration\Exception\RegistrationException;
use Throwable;

class MissingTokenConfirmationException extends ConfirmationException
{

    public function __construct()
    {
        parent::__construct(
            'In order for the confirmation mailer to send an e-mail, the registration confirmation token must have been previously assigned to the mailer object.'
        );
    }

}