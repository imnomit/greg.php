<?php

namespace nomit\Serialization\Normalization\Internal;

use nomit\Serialization\SerializerInterface;

class DateIntervalSerializer
{
    
    public static function serialize(SerializerInterface $serializer, \DateInterval $dateInterval): mixed
    {
        return array(
            SerializerInterface::CLASS_IDENTIFIER_KEY => \get_class($dateInterval),
            'construct' => array(
                SerializerInterface::SCALAR_TYPE => 'string',
                SerializerInterface::SCALAR_VALUE => \sprintf(
                    'P%sY%sM%sDT%sH%sM%sS',
                    $dateInterval->y,
                    $dateInterval->m,
                    $dateInterval->d,
                    $dateInterval->h,
                    $dateInterval->i,
                    $dateInterval->s
                ),
            ),
            'invert' => array(
                SerializerInterface::SCALAR_TYPE => 'integer',
                SerializerInterface::SCALAR_VALUE => (empty($dateInterval->invert)) ? 0 : 1,
            ),
            'days' => array(
                SerializerInterface::SCALAR_TYPE => \gettype($dateInterval->days),
                SerializerInterface::SCALAR_VALUE => $dateInterval->days,
            ),
        );
    }

    public static function unserialize(SerializerInterface $serializer, string $className, array $value): object
    {
        $ref = new \ReflectionClass($className);

        return self::fillObjectProperties(self::getTypedValue($serializer, $value), $ref);
    }

    protected static function fillObjectProperties(array $value, \ReflectionClass $ref): object
    {
        $obj = $ref->newInstanceArgs([$value['construct']]);
        
        unset($value['construct']);

        foreach ($value as $k => $v) {
            $obj->$k = $v;
        }

        return $obj;
    }
    
    protected static function getTypedValue(SerializerInterface $serializer, array $value): mixed
    {
        foreach ($value as &$v) {
            $v = $serializer->unserialize($v);
        }

        return $value;
    }

}