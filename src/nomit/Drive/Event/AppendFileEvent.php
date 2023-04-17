<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;

class AppendFileEvent extends FileEvent
{

    public function __construct(
        FileSystemInterface $fileSystem,
        FileInterface $file,
        protected readonly string $content,
        protected readonly bool $created
    )
    {
        parent::__construct($fileSystem, $file);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isCreated(): bool
    {
        return $this->created;
    }

}