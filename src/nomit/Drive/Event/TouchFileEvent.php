<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;

class TouchFileEvent extends FileEvent
{

    public function __construct(
        FileSystemInterface                   $fileSystem,
        FileInterface                         $file,
        protected readonly \DateTimeInterface $modificationDateTime,
        protected readonly \DateTimeInterface $accessDateTime,
        protected readonly bool               $created
    )
    {
        parent::__construct($fileSystem, $file);
    }

    public function getModificationDateTime(): \DateTimeInterface
    {
        return $this->modificationDateTime;
    }

    public function getAccessDateTime(): \DateTimeInterface
    {
        return $this->accessDateTime;
    }

    public function isCreated(): bool
    {
        return $this->created;
    }

}