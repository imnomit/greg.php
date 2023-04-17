<?php

namespace nomit\Utility\Enumeration;

use nomit\Utility\Bitmask\BitmaskUtility;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\EnumerationUtility;

trait EquatableEnumerationTrait
{

    public static function equals(mixed $property, EnumerationInterface $enumeration): bool
    {
        return EnumerationUtility::equals($property, $enumeration);
    }

    public function is(mixed $property): bool
    {
        return self::equals($property, $this);
    }

    public function supports(mixed $property): bool
    {
        return EnumerationUtility::supports($property, $this);
    }

}