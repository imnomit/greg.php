<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;

class TruncateFileEvent extends FileEvent
{

    public function __construct(
        FileSystemInterface $fileSystem,
        FileInterface $file,
        protected readonly int $size
    )
    {
        parent::__construct($fileSystem, $file);
    }

    public function getSize(): int
    {
        return $this->size;
    }

}