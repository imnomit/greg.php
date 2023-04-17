<?php

namespace Application\Summary\Administration;

use nomit\Utility\Concern\Arrayable;

interface SummaryExtensionInterface extends Arrayable
{
    
    public function getName(): string;

    public function set(string $name, mixed $value): self;

    public function has(string $name): bool;

    public function get(string $name, mixed $default = null): mixed;

    public function remove(string $name): void;

    public function all(array $items = null): array|self;

    public function summarize(): void;

}