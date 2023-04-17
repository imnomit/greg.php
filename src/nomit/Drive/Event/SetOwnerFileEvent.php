<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;

class SetOwnerFileEvent extends FileEvent implements SetOwnerEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        FileInterface $file,
        protected readonly int|string $owner
    )
    {
        parent::__construct($fileSystem, $file);
    }

    public function getOwner(): int|string
    {
        return $this->owner;
    }

}