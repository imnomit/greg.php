<?php

namespace nomit\Utility\Enumeration;

use nomit\Utility\EnumerationUtility;

trait SelfAwareEnumerationTrait
{

    public static function getNames(string|\Stringable|EnumerationInterface $enumeration = null): array
    {
        if($enumeration === null) {
            $enumeration = __CLASS__;
        }

        return EnumerationUtility::getNames($enumeration);
    }

    public static function getValues(string|\Stringable|EnumerationInterface $enumeration = null): array
    {
        if($enumeration === null) {
            $enumeration = __CLASS__;
        }

        return EnumerationUtility::getValues($enumeration);
    }

}