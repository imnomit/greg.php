<?php

namespace nomit\Drive\Exception\Stream;

class StreamWrapperException extends StreamException
{

    public function __construct(
        protected readonly string $scheme,
        protected readonly mixed $host,
        string $message,
        \Throwable $previous = null
    )
    {
        parent::__construct(
            $message,
            $previous
        );
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getHost(): mixed
    {
        return $this->host;
    }

}