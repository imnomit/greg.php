<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\ExceptionCodeEnumeration;

class NotFoundFileException extends FileException
{

    public static function fromFilename(string $filename, string $message = null, \Throwable $previous = null): self
    {
        return new self(
            $filename,
            sprintf(
                'No file with the filename "%s" could be found',
                $filename
            )
            . $message
                ? sprintf(': "%s".', $message)
                : '.',
            $previous
        );
    }

    public function __construct(
        string $filename,
        string $message = "",
        ?\Throwable $previous = null
    )
    {
        parent::__construct($filename, $message, ExceptionCodeEnumeration::NOT_FOUND_EXCEPTION, $previous);
    }

}