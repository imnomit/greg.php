<?php

namespace nomit\Drive\Iterator;

use nomit\Utility\Bitmask\BitmaskUtility;
use nomit\Utility\Enumeration\EnumerationInterface;
use nomit\Utility\Enumeration\FlaggableEnumerationInterface;
use nomit\Utility\EnumerationUtility;

enum CurrentModeIteratorEnumeration: int implements FlaggableEnumerationInterface
{

    case CURRENT_AS_PATHNAME = 32;

    case CURRENT_AS_BASENAME = 64;

    case CURRENT_AS_FILE = 0;

    case CURRENT_AS_SELF = 16;

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