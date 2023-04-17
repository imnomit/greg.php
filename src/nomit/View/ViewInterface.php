<?php

namespace nomit\View;

use nomit\Utility\Concern\Stringable;
use nomit\Web\Response\ResponseInterface;

interface ViewInterface extends Stringable
{

    public const LOCATION_HEAD = 4;

    public const LOCATION_BODY = 16;

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

    public function render(): ResponseInterface;

}