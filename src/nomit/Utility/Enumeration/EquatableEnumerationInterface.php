<?php

namespace nomit\Utility\Enumeration;

interface EquatableEnumerationInterface extends EnumerationInterface
{

    public static function equals(mixed $property, EnumerationInterface $enumeration): bool;
    
    public function is(mixed $property): bool;

    public function supports(mixed $property): bool;

}