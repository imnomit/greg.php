<?php

namespace nomit\Drive\Exception;

class OperationException extends Exception
{

    public function __construct(
        string $message = "",
        ?\Throwable $previous = null
    )
    {
        parent::__construct($message, ExceptionCodeEnumeration::OPERATION_EXCEPTION, $previous);
    }

}