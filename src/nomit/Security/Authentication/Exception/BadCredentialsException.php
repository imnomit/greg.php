<?php

namespace nomit\Security\Authentication\Exception;

class BadCredentialsException extends AuthenticationException
{

    public function getMessageKey(): string
    {
        return 'The supplied credentials are invalid.';
    }

}