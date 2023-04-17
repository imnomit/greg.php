<?php

namespace nomit\Drive\Exception\Stream;

use nomit\Drive\Exception\Exception;
use nomit\Drive\Exception\ExceptionCodeEnumeration;

class StreamException extends Exception
{

    public function __construct(
        string $message = "",
        ?\Throwable $previous = null
    )
    {
        parent::__construct(
            $message,
            ExceptionCodeEnumeration::FILESYSTEM_EXCEPTION,
            $previous
        );
    }

}