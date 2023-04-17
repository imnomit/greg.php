<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\OperationException;

class FileOperationException extends OperationException implements FileExceptionInterface
{

    public function __construct(
        private readonly string $filename,
        string $message = "",
        ?\Throwable $previous = null
    )
    {
        parent::__construct($message, $previous);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

}