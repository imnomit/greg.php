<?php

namespace nomit\Drive\Stream;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Stream\StreamInterface;

interface StreamManagerInterface
{

    public static function autoregister(FileSystemInterface $fileSystem): array;

    public static function register(FileSystemInterface $fileSystem, string $host, string $scheme = null): void;

    public static function unregister(string $host, string $scheme, bool $silent = false): void;

    public static function search(string $host, string $scheme = null): FileSystemInterface;

    public static function registerStream(StreamInterface $stream): int;

    public static function unregisterStream(StreamInterface $stream): void;

    public static function searchStream(int $index): StreamInterface;

    public static function free(): void;

}