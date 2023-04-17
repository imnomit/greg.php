<?php

namespace nomit\Drive\Exception\Stream;

class AlreadyRegisteredStreamWrapperException extends StreamWrapperException
{

    public function __construct(
        string $scheme,
        mixed $host,
        \Throwable $previous = null
    )
    {
        parent::__construct(
            $scheme,
            $host,
            sprintf(
                'A stream wrapper has already been registered for the scheme "%s:://%s".',
                $scheme,
                $host
            )
        );
    }

}