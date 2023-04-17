<?php

namespace nomit\Drive\Event;

use nomit\Drive\FileSystemInterface;

interface FileSystemEventInterface
{

    public function getFileSystem(): FileSystemInterface;

}