<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\ResourceInterface;
use nomit\Drive\Utility\OperationEnumeration;

class CopyEvent extends DestinationEvent
{

    public function __construct(
        FileSystemInterface                     $fileSystem,
        ResourceInterface                       $sourceResource,
        ResourceInterface                       $destinationResource,
        protected readonly bool                 $recursive,
        protected readonly OperationEnumeration $overwriteOperation,
        protected readonly bool                 $createParents
    )
    {
        parent::__construct($fileSystem, $sourceResource, $destinationResource);
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
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