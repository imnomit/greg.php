<?php

namespace nomit\Drive\Plugin;

abstract class AbstractPlugin implements PluginInterface
{

    protected string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->getName();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}