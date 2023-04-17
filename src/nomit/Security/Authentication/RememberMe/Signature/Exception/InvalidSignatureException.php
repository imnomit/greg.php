<?php

namespace nomit\Security\Authentication\Signature\Exception;

use nomit\Security\Authentication\Signature\Expection\SignatureException;

class InvalidSignatureException extends SignatureException
{

    public function getMessageKey(): string
    {
        return 'The supplied signature, "%s", is invalid.';
    }

}