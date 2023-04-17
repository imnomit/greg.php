<?php

namespace nomit\Utility\Bitmask;

use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Enumeration\EnumerationInterface;

class Bitmask implements BitmaskInterface
{

    private int $mask;

    public function __construct(
        int|EnumerationInterface|BitmaskInterface $mask
    )
    {
        $this->set($mask);
    }

    final public function add(int|EnumerationInterface|BitmaskInterface $property): BitmaskInterface
    {
        $this->set(BitmaskUtility::add($property, $this->get()));

        return $this;
    }

    final public function set(int|EnumerationInterface|BitmaskInterface $mask): BitmaskInterface
    {
        $this->mask = $mask;

        return $this;
    }

    final public function is(BitmaskInterface|EnumerationInterface|int $property): bool
    {
        return $this->get() === ConcernUtility::toInteger($property);
    }

    final public function has(int|EnumerationInterface|BitmaskInterface $property): bool
    {
        return BitmaskUtility::has($property, $this->get());
    }

    final public function get(): int
    {
        return $this->mask;
    }

    final public function remove(int|EnumerationInterface|BitmaskInterface $property): void
    {
        $this->set(BitmaskUtility::remove($property, $this->get()));
    }

    public function toInteger(): int
    {
        return $this->get();
    }

    public function toString(): string
    {
        return (string) $this->get();
    }

    final public function __toString(): string
    {
        return $this->toString();
    }

}