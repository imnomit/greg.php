<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\FileSystemInterface;

interface FileSystemPluginInterface extends PluginInterface
{

    public function getFileSystem(): FileSystemInterface;

}