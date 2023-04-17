<?php

namespace nomit\Drive\Event;

interface SetModeEventInterface extends FileSystemEventInterface
{

    public function getMode(): int|string;

}