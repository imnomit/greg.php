<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;

class SetModeFileEvent extends FileEvent implements SetModeEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        FileInterface $file,
        protected readonly int|string $mode
    )
    {
        parent::__construct($fileSystem, $file);
    }

    public function getMode(): int|string
    {
        return $this->mode;
    }

}