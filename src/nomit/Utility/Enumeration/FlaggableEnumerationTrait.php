<?php

namespace nomit\Utility\Enumeration;

use nomit\Utility\Bitmask\BitmaskUtility;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\EnumerationUtility;

trait FlaggableEnumerationTrait
{

    use EquatableEnumerationTrait;

    public static function equals(mixed $property, EnumerationInterface $enumeration): bool
    {
        return EnumerationUtility::equals($property, $enumeration)
            || BitmaskUtility::has(ConcernUtility::toInteger($property), ConcernUtility::toInteger($enumeration));
    }


    public function has(mixed $property): bool
    {
        return $this->is($property)
            || BitmaskUtility::has($property, ConcernUtility::toInteger($this));
    }

}