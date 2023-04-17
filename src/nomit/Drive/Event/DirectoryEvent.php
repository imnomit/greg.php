<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;

class DirectoryEvent extends FileSystemEvent implements DirectoryEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        protected readonly DirectoryInterface $directory
    )
    {
    }

    public function getDirectory(): DirectoryInterface
    {
        return $this->directory;
    }

}