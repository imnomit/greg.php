<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\OperationException;

class DirectoryOperationException extends OperationException implements DirectoryExceptionInterface
{

    public function __construct(
        private readonly string $pathname,
        string $message = "",
        ?\Throwable $previous = null
    )
    {
        parent::__construct($message, $previous);
    }


    public function getPathname(): string
    {
        return $this->pathname;
    }

}