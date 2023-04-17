<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\File\FileInterface;

interface FilePluginInterface extends ResourcePluginInterface
{

    public function getFile(): FileInterface;

}