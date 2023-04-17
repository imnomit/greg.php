<?php

namespace nomit\Utility\Concern;

use nomit\Exception\InvalidArgumentException;
use nomit\Serialization\Normalization\Closure\SerializableClosure;
use nomit\Utility\DateTime;

final class ConcernUtility
{

    public static function isIntegerable(mixed $subject): bool
    {
        return is_numeric($subject)
            || $subject instanceof Integerable
            || method_exists($subject, 'toInteger');
    }

    public static function isStringable(mixed $subject): bool
    {
        return is_string($subject)
            || $subject instanceof \Stringable
            || method_exists($subject, '__toString');
    }

    public static function isIterable(mixed $subject): bool
    {
        return self::isTraversable($subject)
            || is_iterable($subject)
            || $subject instanceof Arrayable
            || method_exists($subject, '__toArray');
    }

    public static function isTraversable(mixed $subject): bool
    {
        return self::isIterable($subject)
            || $subject instanceof \Traversable;
    }


    public static function isDateTime(mixed $subject): bool
    {
        return self::isIntegerable($subject)
            || self::isStringable($subject)
            || $subject instanceof \DateTimeInterface;
    }

    public static function isCallable(mixed $subject): bool
    {
        return is_callable($subject)
            || $subject instanceof \Closure
            || $subject instanceof SerializableClosure;
    }

    public static function toInteger(mixed $subject): int
    {
        if($subject instanceof \UnitEnum) {
            $subject = $subject->value;
        }

        if(!self::isIntegerable($subject)) {
            throw new InvalidArgumentException(sprintf('The supplied value, of type "%s", is neither an integer nor an integer-convertable object, and so could not be casted as an integer.', get_debug_type($subject)));
        }

        if($subject instanceof Integerable) {
            $subject = $subject->toInteger();
        }

        return (int) $subject;
    }

    public static function toString(mixed $subject): string
    {
        if($subject instanceof \UnitEnum) {
            $subject = $subject->value;
        }

        if(!self::isStringable($subject)) {
            throw new InvalidArgumentException(sprintf('The supplied value, of type "%s", is neither a string nor a string-convertable object, and so could not be casted as a string.', get_debug_type($subject)));
        }

        if($subject instanceof Stringable) {
            $subject = $subject->toString();
        } else if($subject instanceof \Stringable || method_exists($subject, '__toString')) {
            $subject = $subject->__toString();
        }

        return (string) $subject;
    }

    public static function toIterable(mixed $subject): iterable
    {
        if($subject instanceof \UnitEnum) {
            $subject = $subject->value;
        }

        if(!self::isIterable($subject)) {
            $subject = [$subject];
        }

        if($subject instanceof Arrayable) {
            $subject = $subject->toArray();
        } else if(method_exists($subject, '__toArray')) {
            $subject = $subject->__toArray();
        }

        if(is_iterable($subject)) {
            return iterator_to_array($subject);
        }

        if(self::isTraversable($subject)) {
            return $subject;
        }

        return (array) $subject;
    }

    public static function toDateTime(mixed $subject): \DateTimeInterface
    {
        if(!self::isDateTime($subject)) {
            throw new InvalidArgumentException(sprintf('The supplied value, of type "%s", is neither a date-time nor a date-time-convertable object, and so could not be casted as a date-time.', get_debug_type($subject)));
        }

        if($subject instanceof \DateTimeInterface) {
            return $subject;
        }

        if(self::isIntegerable($subject)) {
            return DateTime::from(self::toInteger($subject));
        }

        if(self::isStringable($subject)) {
            return DateTime::from(self::toString($subject));
        }

        throw new InvalidArgumentException(sprintf('The supplied value, of type "%s", is neither a date-time nor a date-time-convertable object, and so could not be casted as a date-time.', get_debug_type($subject)));
    }

    public static function toCallback(mixed $subject): callable
    {
        if(!self::isCallable($subject)) {
            throw new InvalidArgumentException(sprintf('The supplied value, of type "%s", is not callable, and so could not be converted to a callback function.', get_debug_type($subject)));
        }

        if($subject instanceof SerializableClosure) {
            $subject = $subject->getClosure();
        }

        return $subject;
    }

}