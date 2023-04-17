<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\EventDispatcher\Event;

class FileSystemEvent extends Event implements FileSystemEventInterface
{

    public function __construct(
        protected readonly FileSystemInterface $fileSystem
    )
    {
    }

    public function getFileSystem(): FileSystemInterface
    {
        return $this->fileSystem;
    }

}