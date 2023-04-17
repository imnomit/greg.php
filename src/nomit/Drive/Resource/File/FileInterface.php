<?php

namespace nomit\Drive\Resource\File;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\ResourceInterface;
use nomit\Drive\Stream\StreamInterface;
use nomit\Stream\StreamModeInterface;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;

interface FileInterface extends ResourceInterface {

    public function getFileSystem(): FileSystemInterface;

    public function isExecutable(): bool;

    public function write(string $content, bool $create = true): int;

    public function append(string $content, bool $create = true): int;

    public function read(): string;

    public function truncate(int $size = 0): self;

    public function getStream(StreamModeInterface|string|Stringable $mode = null): StreamInterface;

    public function getStreamUrl(): string;

    public function touch(string|Stringable|int|Integerable|\DateTimeInterface $modificationTime = 'NOW', string|Stringable|int|Integerable|\DateTimeInterface $accessTime = null, bool $create = true): bool;

}