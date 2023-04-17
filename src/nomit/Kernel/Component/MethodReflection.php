<?php

namespace nomit\Kernel\Component;

use nomit\Utility\Object\SmartObjectTrait;

class MethodReflection extends \ReflectionMethod
{

    use SmartObjectTrait;

    public function hasAnnotation(string $name): bool
    {
        return (bool) ComponentReflection::parseAnnotation($this, $name);
    }

    public function getAnnotation(string $name): mixed
    {
        $result = ComponentReflection::parseAnnotation($this, $name);

        return $result ? end($result) : null;
    }

}