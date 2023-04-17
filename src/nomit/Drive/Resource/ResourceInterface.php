<?php

namespace nomit\Drive\Resource;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Plugin\FilePluginProviderInterface;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Plugin\ResourcePluginInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Utility\OperationEnumeration;
use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;

interface ResourceInterface extends Stringable,
    \IteratorAggregate,
    \Countable
{

    public function getFileSystem(): FileSystemInterface;

    public function getPathname(bool $asString = true): string|PathnameInterface;

    public function getBasename(string $suffix = null): string;

    public function getExtension(): string;

    public function getDirectory(): string;

    public function getParent(): ?self;

    public function isFile(): bool;

    public function isDirectory(): bool;

    public function isLink(): bool;

    public function setAccessTime(string|Stringable|int|Integerable|\DateTimeInterface $dateTime = null): self;

    public function getAccessTime(): \DateTimeInterface;

    public function getCreationTime(): \DateTimeInterface;

    public function setModificationTime(string|Stringable|int|Integerable|\DateTimeInterface $dateTime = null): self;

    public function getModificationTime(): \DateTimeInterface;

    public function getSize(): int|false;

    public function setOwner(string|int $user): self;

    public function getOwner(): int|string;

    public function setGroup(int|string $group): self;

    public function getGroup(): int|string;

    public function setMode(int $mode): self;

    public function getMode(): int;

    public function isReadable(): bool;

    public function isWritable(): bool;

    public function exists(): bool;

    public function delete(bool $recursive = false, bool $force = false): bool;

    public function copyTo(self $destination, bool $recursive = false, bool $overwrite = false, bool $createParents = false): self;

    public function moveTo(self $destination, bool $overwrite = false, bool $createParents = false): self;

    public function createDirectory(bool $createParents = false): DirectoryInterface;

    public function createFile(bool $createParents = false): FileInterface;

    public function hasPlugin(string|Stringable $name): bool;

    public function getPlugin(string|Stringable $name): ?ResourcePluginInterface;

}