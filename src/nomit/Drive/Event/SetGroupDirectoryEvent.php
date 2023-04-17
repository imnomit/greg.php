<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;

class SetGroupDirectoryEvent extends DirectoryEvent implements SetGroupEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        DirectoryInterface $directory,
        protected readonly int|string $group
    )
    {
        parent::__construct($fileSystem, $directory);
    }

    public function getGroup(): int|string
    {
        return $this->group;
    }

}