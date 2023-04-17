<?php

namespace nomit\Drive\Event;

use nomit\Drive\Resource\File\FileInterface;

interface FileEventInterface extends FileSystemEventInterface
{

    public function getFile(): FileInterface;

}