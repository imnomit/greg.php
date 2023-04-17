<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Exception\Plugin\SupportlessPluginProviderException;
use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\ResourceInterface;

abstract class AbstractPluginProvider implements PluginProviderInterface
{

    public function providesFileSystem(FileSystemInterface $fileSystem): bool
    {
        return false;
    }

    public function providesResource(ResourceInterface $resource): bool
    {
        return false;
    }

    public function fromFileSystem(FileSystemInterface $fileSystem): FileSystemPluginInterface
    {
        throw new SupportlessPluginProviderException(
            $this,
            FileSystemPluginInterface::class
        );
    }

    public function fromResource(ResourceInterface $resource): ResourcePluginInterface
    {
        throw new SupportlessPluginProviderException(
            $this,
            ResourcePluginInterface::class
        );
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