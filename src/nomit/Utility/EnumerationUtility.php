<?php

namespace nomit\Utility;

use nomit\Exception\InvalidArgumentException;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;

final class EnumerationUtility
{

    public static function equals(mixed $test, mixed $subject): bool
    {
        if(ConcernUtility::isIterable($test) && ConcernUtility::isIterable($subject)) {
            [$test, $subject] = [ConcernUtility::toArray($test), ConcernUtility::toArray($subject)];

            return count($test) === count($subject)
                && array_diff($test, $subject) === array_diff($subject, $test);
        }

        if(ConcernUtility::isIntegerable($test) && ConcernUtility::isIntegerable($subject)) {
            [$test, $subject] = [ConcernUtility::toInteger($test), ConcernUtility::toInteger($subject)];
        }

        if(ConcernUtility::isStringable($test) && ConcernUtility::isStringable($subject)) {
            [$test, $subject] = [strtolower(ConcernUtility::toString($test)), strtolower(ConcernUtility::toString($subject))];
        }

        return $test === $subject;
    }

    public static function supports(mixed $test, \UnitEnum|string|\Stringable $enumeration): bool
    {
        self::assertExistence($enumeration);

        foreach(self::getValues($enumeration) as $case) {
            if(self::equals($test, $case)) {
                return true;
            }
        }

        return false;
    }

    public static function getNames(\UnitEnum|string|\Stringable $enumeration): array
    {
        self::assertExistence($enumeration);

        return array_map(function(\UnitEnum $enumeration) {
            return $enumeration->name;
        }, $enumeration::cases());
    }

    public static function getValues(\UnitEnum|string|\Stringable $enumeration): array
    {
        self::assertExistence($enumeration);

        return array_map(function (\UnitEnum $enumeration) {
            return $enumeration->value;
        }, $enumeration::cases());
    }

    private static function assertExistence(\UnitEnum|string|\Stringable $enumeration): void
    {
        if((is_string($enumeration) || $enumeration instanceof \Stringable)
            && !enum_exists($enumeration)
        ) {
            throw new InvalidArgumentException(sprintf('The enumeration referenced by the supplied classname, "%s", does not exist.', $enumeration));
        }
    }

}