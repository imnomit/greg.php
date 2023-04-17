<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Resource\ResourceInterface;

class UnwritableResourceException extends ResourceException
{

    public function __construct(
        protected readonly string $pathname,
        ?\Throwable $previous = null
    )
    {
        parent::__construct(
            sprintf('The resource represented by the supplied pathname, "%s", is not writable.', $this->pathname),
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            $previous
        );
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

}