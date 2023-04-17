<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\ResourceInterface;
use nomit\Utility\Concern\Stringable;

interface PluginProviderInterface extends Stringable
{

    public function getName(): string;

    public function providesFileSystem(FileSystemInterface $fileSystem): bool;

    public function providesResource(ResourceInterface $resource): bool;

    public function fromFileSystem(FileSystemInterface $fileSystem): FileSystemPluginInterface;

    public function fromResource(ResourceInterface $resource): ResourcePluginInterface;

}