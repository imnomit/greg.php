<?php

namespace nomit\Utility\Bitmask;

use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Enumeration\EnumerationInterface;

final class BitmaskUtility
{

    public static function add(int|EnumerationInterface|Integerable $property, int|EnumerationInterface|Integerable $subject): int
    {
        [$property, $subject] = [ConcernUtility::toInteger($property), ConcernUtility::toInteger($subject)];

        return $subject & $property;
    }

    public static function has(int|EnumerationInterface|Integerable $property, int|EnumerationInterface|Integerable|iterable $subject): bool
    {
        if(ConcernUtility::isTraversable($subject)) {
            foreach($subject as $test) {
                if(self::has($property, $test)) {
                    return true;
                }
            }

            return false;
        }

        [$property, $subject] = [ConcernUtility::toInteger($property), ConcernUtility::toInteger($subject)];

        return $property === $subject
            || ($property & $subject) === $subject;
    }

    public static function remove(int|EnumerationInterface|Integerable $property, int|EnumerationInterface|Integerable $subject): int
    {
        [$property, $subject] = [ConcernUtility::toInteger($property), ConcernUtility::toInteger($subject)];

        $subject &= ~$property;

        return $subject;
    }

}