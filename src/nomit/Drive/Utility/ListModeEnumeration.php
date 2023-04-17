<?php

namespace nomit\Drive\Utility;

use nomit\Utility\Enumeration\FlaggableEnumerationInterface;
use nomit\Utility\Enumeration\FlaggableEnumerationTrait;
use nomit\Utility\Enumeration\SelfAwareEnumerationInterface;
use nomit\Utility\Enumeration\SelfAwareEnumerationTrait;

enum ListModeEnumeration: int implements SelfAwareEnumerationInterface,
    FlaggableEnumerationInterface
{

    use SelfAwareEnumerationTrait,
        FlaggableEnumerationTrait;

    case LIST_ALL = 64512;

    case LIST_HIDDEN = 1024;

    case LIST_VISIBLE = 2048;

    case LIST_FILES = 4096;

    case LIST_DIRECTORIES = 8192;

    case LIST_LINKS = 16384;

    case LIST_OPAQUE = 32768;

    case LIST_RECURSIVE = 65536;

    public function isListingAll(): bool
    {
        return $this->has(self::LIST_ALL);
    }

    public function isListingHidden(): bool
    {
        return $this->has(self::LIST_HIDDEN);
    }

    public function isListingVisible(): bool
    {
        return $this->has(self::LIST_VISIBLE);
    }

    public function isListingFiles(): bool
    {
        return $this->has(self::LIST_FILES);
    }

    public function isListingDirectories(): bool
    {
        return $this->has(self::LIST_DIRECTORIES);
    }

    public function isListingLinks(): bool
    {
        return $this->has(self::LIST_LINKS);
    }

    public function isListingOpaque(): bool
    {
        return $this->has(self::LIST_OPAQUE);
    }

    public function isListingRecursive(): bool
    {
        return $this->has(self::LIST_RECURSIVE);
    }

}