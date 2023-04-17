<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Resource\ResourceInterface;

abstract class AbstractFilePlugin extends AbstractResourcePlugin implements FilePluginInterface
{

    public function __construct(
        FileInterface $resource
    )
    {
        parent::__construct($resource);
    }

    public function getFile(): FileInterface
    {
        return $this->resource;
    }

}