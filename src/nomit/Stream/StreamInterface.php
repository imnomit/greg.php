<?php

namespace nomit\Stream;

use nomit\Utility\Concern\Stringable;

interface StreamInterface extends \Psr\Http\Message\StreamInterface, Stringable
{

    public function getMode(): string;

    public function hasResource(): bool;

    public function getResource();

    public function lock(int $mode): self;

    public function isLocked(): bool;

    public function unlock(): self;

    public function getHash(string $algorithm = 'sha256', bool $raw = false): string;

    public function copyTo($handle, int $offset = 0, int $length = null): false|int;

    public function copyToStream(self $stream, int $offset = 0, ?int $length = null): false|int;

    public function passthru(int $offset = 0, ?int $length = null, int $bufferSize = 1024): void;

    public function setMetadata(array $metadata): self;

    public function truncate(int $size): bool;

    public function flush(): bool;

}