<?php

namespace nomit\Drive\Iterator;

use nomit\Utility\Bitmask\BitmaskUtility;
use nomit\Utility\Enumeration\EnumerationInterface;
use nomit\Utility\Enumeration\FlaggableEnumerationInterface;
use nomit\Utility\EnumerationUtility;

enum KeyModeIteratorEnumeration: int implements FlaggableEnumerationInterface
{

    case KEY_AS_PATHNAME = 0;

    case KEY_AS_FILENAME = 256;

    public static function equals(mixed $property, EnumerationInterface $enumeration): bool
    {
        return EnumerationUtility::equals($property, $enumeration);
    }

    public function has(mixed $property): bool
    {
        return BitmaskUtility::has($property, $this);
    }

    public function is(mixed $property): bool
    {
        return EnumerationUtility::equals($property, $this);
    }

    public function supports(mixed $property): bool
    {
        return EnumerationUtility::supports($property, $this);
    }

}