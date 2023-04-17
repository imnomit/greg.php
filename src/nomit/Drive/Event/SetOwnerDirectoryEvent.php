<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;

class SetOwnerDirectoryEvent extends DirectoryEvent implements SetOwnerEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        DirectoryInterface $directory,
        protected readonly int|string $owner
    )
    {
        parent::__construct($fileSystem, $directory);
    }

    public function getOwner(): int|string
    {
        return $this->owner;
    }

}