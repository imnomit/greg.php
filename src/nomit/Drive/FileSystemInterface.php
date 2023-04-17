<?php

namespace nomit\Drive;

use nomit\Drive\Adapter\PrimaryAdapter;
use nomit\Drive\Plugin\PluginManagerInterface;
use nomit\Drive\Plugin\FileSystemPluginInterface;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Resource\ResourceInterface;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Utility\Concern\Stringable;

interface FileSystemInterface
{

    public function getPrimaryAdapter(): PrimaryAdapter;

    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null): self;

    public function getEventDispatcher(): ?EventDispatcherInterface;

    public function setPluginManager(PluginManagerInterface $manager = null): self;

    public function getPluginManager(): ?PluginManagerInterface;

    public function enableStreaming(string $host, string $scheme = null): self;

    public function disableStreaming(): void;

    public function isStreamingEnabled(): bool;

    public function getStreamHost(): ?string;

    public function getStreamScheme(): ?string;

    public function getStreamPrefix(): ?string;

    public function hasPlugin(string|Stringable $name): bool;

    public function getPlugin(string|Stringable $name): ?FileSystemPluginInterface;

    public function getFileRoot(): FileInterface;

    public function getFile(string|Stringable|PathnameInterface $pathname = null): FileInterface;

    public function getDirectory(string|Stringable|PathnameInterface $pathname = null): DirectoryInterface;

}