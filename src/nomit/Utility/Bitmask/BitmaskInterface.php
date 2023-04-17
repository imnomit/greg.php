<?php

namespace nomit\Utility\Bitmask;

use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;
use nomit\Utility\Enumeration\EnumerationInterface;

interface BitmaskInterface extends Integerable
{

    public function add(int|EnumerationInterface|self $property): self;

    public function set(int|EnumerationInterface|self $mask): self;

    public function is(int|EnumerationInterface|self $property): bool;

    public function has(int|EnumerationInterface|self $property): bool;

    public function get(): int;

    public function remove(int|EnumerationInterface|self $property): void;

}