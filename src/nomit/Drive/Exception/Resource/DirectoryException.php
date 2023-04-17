<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\ExceptionCodeEnumeration;

class DirectoryException extends ResourceException implements DirectoryExceptionInterface
{

    public function __construct(
        private readonly string      $pathname,
        string                       $message = "",
        int|ExceptionCodeEnumeration $code = 0,
        ?\Throwable                  $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function toString(): string
    {
        return $this->getPathname();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}