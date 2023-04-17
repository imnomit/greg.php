<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\FileSystemInterface;

abstract class AbstractFileSystemPlugin extends AbstractPlugin implements FileSystemPluginInterface
{

    public function __construct(
        protected FileSystemInterface $fileSystem
    )
    {
    }

    public function getFileSystem(): FileSystemInterface
    {
        return $this->fileSystem;
    }

}