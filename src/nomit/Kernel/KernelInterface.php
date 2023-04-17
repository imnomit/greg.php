<?php

namespace nomit\Kernel;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface KernelInterface
{

    public const MAIN_REQUEST = 1;
    public const SUB_REQUEST = 2;

    public function setDebug(bool $debug = true): self;

    public function isDebug(): bool;

    public function run(RequestInterface $request = null): void;

    public function handle(RequestInterface $request, int $type = self::MAIN_REQUEST): ResponseInterface;

}