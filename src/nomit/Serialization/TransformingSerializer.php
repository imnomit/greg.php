<?php

namespace nomit\Serialization;

class TransformingSerializer extends Serializer
{

    protected function serializeObject(mixed $value): array
    {
        if (self::$objectStorage->contains($value)) {
            return self::$objectStorage[$value];
        }

        $reflection = new \ReflectionClass($value);
        $className = $reflection->getName();

        $serialized = $this->serializeInternalClass($value, $className, $reflection);

        self::$objectStorage->attach($value, $serialized);

        return $serialized;
    }

}