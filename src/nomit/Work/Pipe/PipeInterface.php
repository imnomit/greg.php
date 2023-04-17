<?php

namespace nomit\Work\Pipe;

interface PipeInterface
{

    public function setBlock(bool $block = true): void;

    public function read(int $size = 1024): string;

    public function write(string $message): int;

    public function close(): void;

    public function remove(): void;

}