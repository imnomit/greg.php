<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\ExceptionCodeEnumeration;

class NotFoundDirectoryException extends DirectoryException
{

    public static function fromPathname(string $pathName, string $message = null, \Throwable $previous = null): self
    {
        return new self(
            $pathName,
            sprintf(
                'No directory with the pathname "%s" could be found',
                $pathName
            )
                . $message
                    ? sprintf(': "%s".', $message)
                    : '.',
            $previous
        );
    }

    public function __construct(
        string $pathname,
        string $message = "",
        ?\Throwable $previous = null
    )
    {
        parent::__construct($pathname, $message, ExceptionCodeEnumeration::NOT_FOUND_EXCEPTION, $previous);
    }

}