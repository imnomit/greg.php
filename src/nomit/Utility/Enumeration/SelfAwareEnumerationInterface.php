<?php

namespace nomit\Utility\Enumeration;

interface SelfAwareEnumerationInterface extends EnumerationInterface
{

    public static function getNames(string|\Stringable|EnumerationInterface $enumeration = null): array;

    public static function getValues(string|\Stringable|EnumerationInterface $enumeration = null): array;

}