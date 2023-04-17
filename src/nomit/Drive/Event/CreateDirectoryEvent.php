<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Drive\Resource\ResourceInterface;

class CreateDirectoryEvent extends FileSystemEvent
{

    public function __construct(
        FileSystemInterface $fileSystem,
        protected readonly ResourceInterface $resource,
        protected readonly bool $createParents
    )
    {
        parent::__construct($fileSystem);
    }

    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    public function isCreateParents(): bool
    {
        return $this->createParents;
    }

}