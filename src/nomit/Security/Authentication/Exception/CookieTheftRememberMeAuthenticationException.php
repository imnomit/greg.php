<?php

namespace nomit\Security\Authentication\Exception;

class CookieTheftRememberMeAuthenticationException extends RememberMeAuthenticationException
{

    public function getMessageKey(): string
    {
        return 'THe current "remember-me" cookie has already been consumed by another user. It is possible the account has been compromised.';
    }

}