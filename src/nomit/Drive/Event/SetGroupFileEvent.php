<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;

class SetGroupFileEvent extends FileEvent implements SetGroupEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        FileInterface $file,
        protected readonly int|string $group
    )
    {
        parent::__construct($fileSystem, $file);
    }

    public function getGroup(): int|string
    {
        return $this->group;
    }

}