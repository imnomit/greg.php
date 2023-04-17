<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\ResourceInterface;

abstract class AbstractResourcePlugin extends AbstractPlugin implements ResourcePluginInterface
{

    public function __construct(
        protected ResourceInterface $resource
    )
    {
    }

    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

}