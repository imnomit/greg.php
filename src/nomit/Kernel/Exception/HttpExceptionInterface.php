<?php

namespace nomit\Kernel\Exception;

interface HttpExceptionInterface extends ExceptionInterface
{

    public function getStatusCode(): int;

    public function setHeaders(array $headers): self;

    public function getHeaders(): array;

}