<?php

namespace nomit\Drive\Event;

use nomit\Drive\Resource\Directory\DirectoryInterface;

interface DirectoryEventInterface extends FileSystemEventInterface
{

    public function getDirectory(): DirectoryInterface;

}