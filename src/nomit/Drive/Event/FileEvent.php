<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;

class FileEvent extends FileSystemEvent implements FileEventInterface
{

    public function __construct(
        FileSystemInterface $fileSystem,
        protected readonly FileInterface $file
    )
    {
        parent::__construct($fileSystem);
    }

    public function getFile(): FileInterface
    {
        return $this->file;
    }

}