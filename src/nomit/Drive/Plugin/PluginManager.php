<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Exception\Plugin\AlreadyRegisteredPluginManagerException;
use nomit\Drive\Exception\Plugin\UnregisteredPluginManagerException;
use nomit\Utility\Concern\Stringable;

final class PluginManager implements PluginManagerInterface
{

    protected array $plugins = [];

    public function add(PluginProviderInterface $plugin): PluginManagerInterface
    {
        $name = $plugin->getName();

        if($this->has($name)) {
            throw new AlreadyRegisteredPluginManagerException(
                $name,
                $this
            );
        }

        $this->plugins[$name] = $plugin;

        return $this;
    }

    public function has(string|Stringable $name): bool
    {
        return isset($this->plugins[(string) $name]);
    }

    public function get(string|Stringable $name): PluginProviderInterface
    {
        if(!$this->has($name)) {
            throw new UnregisteredPluginManagerException(
                $name,
                $this
            );
        }

        return $this->plugins[(string) $name];
    }

    public function remove(string|Stringable $name): void
    {
        if(!$this->has($name)) {
            throw new UnregisteredPluginManagerException(
                $name,
                $this
            );
        }

        unset($this->plugins[(string) $name]);
    }

    public function all(): array
    {
        return array_values($this->plugins);
    }

    public function toArray(): array
    {
        return $this->plugins;
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}