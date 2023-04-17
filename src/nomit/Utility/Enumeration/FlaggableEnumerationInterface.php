<?php

namespace nomit\Utility\Enumeration;

interface FlaggableEnumerationInterface extends EquatableEnumerationInterface
{

    public function has(mixed $property): bool;

}