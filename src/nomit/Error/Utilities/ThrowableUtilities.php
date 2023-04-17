<?php

namespace nomit\Error\Utilities;

use nomit\Error\Error\SilencedErrorContext;

final class ThrowableUtilities
{

    public static function getSeverity(SilencedErrorContext|\Throwable $throwable): int
    {
        if ($throwable instanceof \ErrorException || $throwable instanceof SilencedErrorContext) {
            return $throwable->getSeverity();
        }

        if ($throwable instanceof \ParseError) {
            return \E_PARSE;
        }

        if ($throwable instanceof \TypeError) {
            return \E_RECOVERABLE_ERROR;
        }

        return \E_ERROR;
    }

}