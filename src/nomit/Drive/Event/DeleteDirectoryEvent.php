<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;

class DeleteDirectoryEvent extends DirectoryEvent
{

    public function __construct(
        FileSystemInterface $fileSystem,
        DirectoryInterface $directory,
        protected readonly bool $recursive
    )
    {
        parent::__construct($fileSystem, $directory);
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }

}