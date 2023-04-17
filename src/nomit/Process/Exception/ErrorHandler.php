<?php

namespace nomit\Process\Exception;

class ErrorHandler
{

    public function __invoke($severity, $message, $filename, $line)
    {
        if (0 === error_reporting()) {
            return;
        }

        throw new ErrorException($message, 0, $severity, $filename, $line);
    }

}