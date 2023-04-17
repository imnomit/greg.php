<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\OperationException;
use nomit\Drive\Pathname\PathnameInterface;

class OverwriteFileOperationException extends OperationException
{

    public function __construct(
        protected readonly PathnameInterface $sourcePathname,
        protected readonly PathnameInterface $destinationPathname,
        \Throwable $previous = null
    )
    {
        parent::__construct(
            sprintf('An error occurred while attempting to overwrite the destination file, filename "%s", with the contents of the source file, filename "%s".', $this->destinationPathname->getPathname(), $this->sourcePathname->getPathname()),
            $previous
        );
    }

    public function getSourcePathname(): PathnameInterface
    {
        return $this->sourcePathname;
    }

    public function getDestinationPathname(): PathnameInterface
    {
        return $this->destinationPathname;
    }

}