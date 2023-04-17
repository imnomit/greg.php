<?php

namespace nomit\Drive\Exception;

use nomit\Utility\Enumeration\EquatableEnumerationInterface;
use nomit\Utility\Enumeration\EquatableEnumerationTrait;
use nomit\Utility\Enumeration\FlaggableEnumerationInterface;
use nomit\Utility\Enumeration\FlaggableEnumerationTrait;
use nomit\Utility\Enumeration\SelfAwareEnumerationInterface;
use nomit\Utility\Enumeration\SelfAwareEnumerationTrait;

enum ExceptionCodeEnumeration: int implements SelfAwareEnumerationInterface,
    FlaggableEnumerationInterface
{

    use SelfAwareEnumerationTrait,
        FlaggableEnumerationTrait;

    case FILESYSTEM_EXCEPTION = 0x00001;

    case OPERATION_EXCEPTION = 0x00010;

    case NOT_FOUND_EXCEPTION = 0x00100;

    case NOT_FILE_EXCEPTION = 0x01000;

    case NOT_DIRECTORY_EXCEPTION = 0x10000;

    case PLUGIN_EXCEPTION = 0x00002;

    public function isFileSystemException(): bool
    {
        return $this->has(self::FILESYSTEM_EXCEPTION);
    }

    public function isOperationException(): bool
    {
        return $this->has(self::OPERATION_EXCEPTION);
    }

    public function isNotFoundException(): bool
    {
        return $this->has(self::NOT_FOUND_EXCEPTION);
    }

    public function isNotFileException(): bool
    {
        return $this->has(self::NOT_FILE_EXCEPTION);
    }

    public function isNotDirectoryException(): bool
    {
        return $this->has(self::NOT_DIRECTORY_EXCEPTION);
    }

    public function isPluginException(): bool
    {
        return $this->has(self::PLUGIN_EXCEPTION);
    }

}