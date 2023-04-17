<?php

namespace nomit\Drive\Event;

interface SetOwnerEventInterface extends FileSystemEventInterface
{

    public function getOwner(): int|string;

}