<?php

namespace nomit\Drive\Plugin;

use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Stringable;

interface PluginManagerInterface extends Arrayable
{

    public function add(PluginProviderInterface $plugin): self;

    public function has(string|Stringable $name): bool;

    public function get(string|Stringable $name): PluginProviderInterface;

    public function remove(string|Stringable $name): void;

    public function all(): array;

}