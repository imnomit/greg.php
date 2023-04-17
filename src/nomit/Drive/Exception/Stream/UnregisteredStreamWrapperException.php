<?php

namespace nomit\Drive\Exception\Stream;

class UnregisteredStreamWrapperException extends StreamWrapperException
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
                'No stream wrapper has been registered for the scheme "%s:://%s".',
                $scheme,
                $host
            )
        );
    }

}