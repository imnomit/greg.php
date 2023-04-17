<?php

namespace nomit\Drive\Event;

interface SetGroupEventInterface extends FileSystemEventInterface
{

    public function getGroup(): int|string;

}