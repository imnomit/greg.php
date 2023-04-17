<?php

namespace nomit\Security\Authentication\Exception;

class ExpiredAccountException extends AccountException
{

    public function getMessageKey(): string
    {
        return 'The account of the user with the username "%s" has expired.';
    }

}