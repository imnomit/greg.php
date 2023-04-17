<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\ResourceInterface;

interface ResourcePluginInterface extends PluginInterface
{

    public function getResource(): ResourceInterface;

}
