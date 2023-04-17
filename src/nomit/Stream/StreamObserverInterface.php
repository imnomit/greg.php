<?php

namespace nomit\Stream;

use nomit\Utility\Concern\Stringable;

interface StreamObserverInterface
{

    public function opened(StreamModeInterface|Stringable|string $mode): void;

    public function closed(): void;

    public function locked(mixed $operation): bool;

    public function positionChanged(int $offset, int $whence = SEEK_SET): mixed;

    public function truncated(int $size = 0): bool;

    public function read(int $count, string $data): string;

    public function written(string $data): mixed;

    public function flushed(): bool;

}