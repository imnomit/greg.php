<?php

namespace nomit\Security\Registration\Confirmation\Exception;

use nomit\Security\Registration\Confirmation\Token\TokenInterface;
use nomit\Security\Registration\Exception\RegistrationException;
use nomit\Security\Registration\RegistrationInterface;
use Throwable;

class ConfirmationTokenException extends ConfirmationException
{

    protected TokenInterface $token;

    /**
     * ConfirmationTokenException constructor.
     * @param TokenInterface $token
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(TokenInterface $token, string $message = '', int $code = 500,
                                Throwable $previous = null
    )
    {
        $this->token = $token;

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

}