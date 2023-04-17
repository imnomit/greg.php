<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\ResourceInterface;
use nomit\Drive\Utility\OperationEnumeration;

class MoveEvent extends FileSystemEvent
{

    public function __construct(
        FileSystemInterface $fileSystem,
        ResourceInterface $sourceResource,
        ResourceInterface $destinationResource,
        protected readonly OperationEnumeration $overwriteOperation,
        protected readonly bool $createParents
    )
    {
        parent::__construct($fileSystem, $sourceResource, $destinationResource);
    }

    public function getOverwriteOperation(): OperationEnumeration
    {
        return $this->overwriteOperation;
    }

    public function isCreateParents(): bool
    {
        return $this->createParents;
    }

}