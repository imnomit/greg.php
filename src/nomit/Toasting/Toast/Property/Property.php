<?php

namespace nomit\Toasting\Toast\Property;

use nomit\Utility\Concern\Serializable;

class Property implements PropertyInterface
{

    public function __construct(
        private string $name,
        private mixed $value = null
    )
    {
    }

    public static function fromArray(array $payload): PropertyInterface
    {
        return new self($payload['name'], $payload['value']);
    }

    public function setName(string $name): PropertyInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValue(mixed $value): PropertyInterface
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'value' => $this->getValue()
        ];
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function __serialize(): array
    {
        return [$this->name, $this->value];
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        [$this->name, $this->value] = $data;
    }

    public function unserialize(string $payload): ?self
    {
        $this->__unserialize(unserialize($payload));
    }

}