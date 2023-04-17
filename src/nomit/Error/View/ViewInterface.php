<?php

namespace nomit\Error\View;

use nomit\Error\Extension\ExtensionInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

interface ViewInterface
{

    public const LOCATION_HEAD = 4;

    public const LOCATION_BODY = 16;

    public function setRequest(RequestInterface $request): self;

    public function getRequest(): ?RequestInterface;

    public function addExtension(ExtensionInterface $extension): self;

    public function getExtensions(): array;

    public function setErrorLimit($limit = \E_ALL): self;

    public function getErrorLimit(): int;

    public function getContentType(): string;

    public function add(array $data): self;

    public function set(string $name, mixed $value): self;

    public function has(string $name): bool;

    public function get(string $name): mixed;

    public function all(): array;

    public function setHeaders(array $headers): self;

    public function addHeaders(array $headers): self;

    public function setHeader(string $name, string $value): self;

    public function hasHeader(string $name): bool;

    public function getHeader(string $name): string;

    public function getHeaders(): array;

    public function render(\Throwable $exception): ResponseInterface;

}