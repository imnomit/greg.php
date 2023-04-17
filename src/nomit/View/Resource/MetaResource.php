<?php

namespace nomit\View\Resource;

use nomit\Dom\Builder\Element;
use nomit\Utility\Arrays;

class MetaResource implements ResourceInterface
{

    public function __construct(
        private array $parameters
    )
    {
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function setParameter(string $name, mixed $value): self
    {
        Arrays::set($this->parameters, $name, $value);

        return $this;
    }

    public function getParameter(string $name): mixed
    {
        return Arrays::get($this->parameters, $name);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function build(): Element
    {
        $element = new Element('meta');

        foreach($this->parameters as $name => $value) {
            $element->attribute($name, $value);
        }

        return $element;
    }

    public function render(): string
    {
        $element = $this->build();

        return $element->toString();
    }

    public function toString(): string
    {
        return $this->render();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}