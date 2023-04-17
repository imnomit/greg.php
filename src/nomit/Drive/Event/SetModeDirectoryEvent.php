<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;

class SetModeDirectoryEvent extends DirectoryEvent implements SetModeEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        DirectoryInterface $directory,
        protected readonly int|string $mode
    )
    {
        parent::__construct($fileSystem, $directory);
    }

    public function getMode(): int|string
    {
        return $this->mode;
    }

}