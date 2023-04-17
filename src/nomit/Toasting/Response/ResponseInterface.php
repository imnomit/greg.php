<?php

namespace nomit\Toasting\Response;

use nomit\Utility\Concern\Arrayable;

interface ResponseInterface extends Arrayable, \Countable
{

    public function getEnvelopes(): array;

    public function getContext(): array;

    public function setOptions(array $options): self;

    public function setOption(string $name, mixed $value): self;

    public function hasOption(string $name): bool;

    public function getOption(string $name): mixed;

    public function getOptions(): array;

}