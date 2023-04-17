<?php

namespace nomit\Drive\Stream;

interface StreamWrapperInterface
{

    public function makeDirectory(string $path, int $mode, int $options): bool;

    public function rename(string $sourcePathname, string $destinationPathname): bool;

    public function deleteDirectory(string $pathname, int $options): bool;

    public function deleteFile(string $pathname): bool;

    public function openDirectory(string $pathname, int $options): bool;

    public function closeDirectory(): bool;

    public function readDirectory(): string|bool;

    public function rewindDirectory(): bool;

    public function open(string $pathname, int $mode, int $options, string &$openedPathname): bool;

    public function close(): void;

    public function cast(int $type);

    public function getMetadata(): array;

    public function getUrlMetadata(string $url, int $flags): false|array;

    public function getStreamMetadata(string $path, int $option, int $variable): bool;

    public function lock(mixed $operation): bool;

    public function seek(int $offset, int $whence = SEEK_SET): bool;

    public function tell(): int;

    public function eof(): bool;

    public function truncate(int $size): bool;

    public function read(int $count): string;

    public function write(string $data): int;

    public function flush(): bool;

}