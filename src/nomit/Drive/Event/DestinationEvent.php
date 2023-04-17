<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Resource\ResourceInterface;

class DestinationEvent extends FileSystemEvent
{

    public function __construct(
        FileSystemInterface              $fileSystem,
        protected readonly ResourceInterface $sourceResource,
        protected readonly ResourceInterface $destinationResource
    )
    {
        parent::__construct($fileSystem);
    }

    public function getSourceResource(): ResourceInterface
    {
        return $this->sourceResource;
    }

    public function getDestinationResource(): ResourceInterface
    {
        return $this->destinationResource;
    }

}