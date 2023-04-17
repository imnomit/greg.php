<?php

namespace nomit\Client\Response;

use nomit\Client\ClientInterface;

interface StreamerInterface
{

    public static function createResource(ResponseInterface $response, ClientInterface $client = null);

    public function getResponse(): ResponseInterface;

    public function bindHandles(&$handle, &$content): void;

    public function open(string $path, string $mode, int $options): bool;

    public function read(int $count): bool|string;

    public function setOption(int $option, int $arg1, ?int $arg2): bool;

    public function tell(): int;

    public function eof(): bool;

    public function seek(int $offset, int $whence = SEEK_SET): bool;

    public function cast(int $castAs);

    public function getStatistics(): array;
}