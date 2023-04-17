<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Drive\Resource\ResourceInterface;

abstract class AbstractDirectoryPlugin extends AbstractResourcePlugin implements DirectoryPluginInterface
{

    public function __construct(
        DirectoryInterface $resource
    )
    {
        parent::__construct($resource);
    }

    public function getDirectory(): DirectoryInterface
    {
        return $this->resource;
    }

}