<?php

namespace nomit\Drive\Utility;

use nomit\Drive\Exception\Resource\DirectoryException;
use nomit\Drive\Exception\Resource\FileException;
use nomit\Drive\Exception\Resource\NotFoundDirectoryException;
use nomit\Drive\Exception\Resource\NotFoundFileException;
use nomit\Drive\Pathname\PathnameInterface;

final class ResourceExistenceAsserter
{

    public static function assertExistence(PathnameInterface $pathname): void
    {
        if(!$pathname->getLocalAdapter()->exists($pathname)) {
            throw ($pathname->getLocalAdapter()->isFile($pathname))
                ? new NotFoundFileException($pathname)
                : new NotFoundDirectoryException($pathname);
        }
    }

    public static function assertFile(PathnameInterface $pathname): void
    {
        self::assertExistence($pathname);

        if(!$pathname->getLocalAdapter()->isFile($pathname)) {
            throw new FileException(
                $pathname,
                sprintf('The resource referenced by the supplied pathname, "%s", is not a file.', $pathname)
            );
        }
    }

    public static function assertDirectory(PathnameInterface $pathname): void
    {
        self::assertExistence($pathname);

        if(!$pathname->getLocalAdapter()->isDirectory($pathname)) {
            throw new DirectoryException(
                $pathname,
                sprintf('The resource referenced by the supplied pathname, "%s", is not a directory.', $pathname)
            );
        }
    }

}