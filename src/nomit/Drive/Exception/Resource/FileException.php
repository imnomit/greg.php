<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\ExceptionCodeEnumeration;

class FileException extends ResourceException implements FileExceptionInterface
{

    public function __construct(
        private readonly string      $filename,
        string                       $message = "",
        int|ExceptionCodeEnumeration $code = ExceptionCodeEnumeration::FILESYSTEM_EXCEPTION,
        ?\Throwable                  $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function toString(): string
    {
        return $this->getFilename();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}