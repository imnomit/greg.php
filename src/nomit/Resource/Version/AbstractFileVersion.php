<?php

namespace nomit\Resource\Version;

use nomit\FileSystem\Directory\Directory;
use nomit\FileSystem\Directory\DirectoryInterface;
use nomit\FileSystem\File\File;
use nomit\FileSystem\File\FileInterface;
use nomit\FileSystem\FileSystem;
use nomit\Resource\Exception\ResourceException;

abstract class AbstractFileVersion extends AbstractVersion
{

    protected FileInterface|DirectoryInterface $file;

    public function __construct(
        FileInterface|DirectoryInterface|string $file,
        string $format
    )
    {
        $this->setFile($file);
        
        parent::__construct($format);
    }

    public function setFile(FileInterface|DirectoryInterface|string $file): self
    {
        if(is_string($file)) {
            if(FileSystem::isFile($file)) {
                $file = new File($file);
            } else if(FileSystem::isDirectory($file)) {
                $file = new Directory($file);
            } else {
                throw new ResourceException(sprintf('The supplied file path, "%s", references a non-existent file or directory.', $file));
            }
        }

        $this->file = $file;

        return $this;
    }

    public function getFile(): FileInterface|DirectoryInterface
    {
        return $this->file;
    }

}