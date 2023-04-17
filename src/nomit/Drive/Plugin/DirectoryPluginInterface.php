<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\Directory\DirectoryInterface;

interface DirectoryPluginInterface extends ResourcePluginInterface
{

    public function getDirectory(): DirectoryInterface;

}