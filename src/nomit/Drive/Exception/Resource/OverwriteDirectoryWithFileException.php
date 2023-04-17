<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Pathname\PathnameInterface;

class OverwriteDirectoryWithFileException extends DirectoryOperationException
{

    public function __construct(
        protected readonly PathnameInterface $sourcePathname,
        protected readonly PathnameInterface $destinationPathname,
        \Throwable $previous = null
    )
    {
        parent::__construct(
            $this->sourcePathname->getPathname(),
            sprintf('An error occurred while attempting to overwrite the destination directory, pathname "%s", with the source file, pathname "%s".', $this->destinationPathname->getPathname(), $this->sourcePathname->getPathname()),
            $previous
        );
    }

    /**
     * @return PathnameInterface
     */
    public function getSourcePathname(): PathnameInterface
    {
        return $this->sourcePathname;
    }

    /**
     * @return PathnameInterface
     */
    public function getDestinationPathname(): PathnameInterface
    {
        return $this->destinationPathname;
    }

}