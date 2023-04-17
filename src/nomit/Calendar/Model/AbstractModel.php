<?php

namespace nomit\Calendar\Model;

use nomit\Utility\Concern\Serializable;

abstract class AbstractModel implements ModelInterface
{

    protected int $id;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    abstract public function __serialize(): array;

    public function unserialize(string $payload): ?self
    {
        $this->__unserialize(unserialize($payload));

        return $this;
    }

    abstract public function __unserialize(array $data): void;

}