<?php

namespace nomit\Resource\Manifest\Store\Manifest;

use nomit\Utility\Collection\Collection;
use nomit\Utility\Collection\CollectionInterface;

class Manifest implements ManifestInterface
{

    protected CollectionInterface $manifest;

    public function __construct(array $manifest)
    {
        $this->setManifest($manifest);
    }

    public function setManifest(array $manifest): ManifestInterface
    {
        $this->manifest = new Collection($manifest);

        return $this;
    }

    public function getManifest(): CollectionInterface
    {
        return $this->manifest;
    }

    public function set(string $name, mixed $value): ManifestInterface
    {
        $this->manifest->set($name, $value);

        return $this;
    }

    public function has(string $name): bool
    {
        return $this->manifest->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->manifest->get($name, $default);
    }

    public function remove(string $name): void
    {
        $this->manifest->remove($name);
    }

    public function toArray(): array
    {
        return $this->manifest->toArray();
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}