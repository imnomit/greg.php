<?php

/*
 |--------------------------------------------------------------------------
 | Values Shorthand Functions Definitions
 |--------------------------------------------------------------------------
 | Function-definitions that provide helpful, value-related functionality.
 |--------------------------------------------------------------------------
 */

namespace nomit;

use Closure;
use Countable;

if (!function_exists('blank')) {
    /**
     * Determine if the given value is "blank".
     *
     * @param mixed $value
     * @return bool
     */
    function is_blank(mixed $value)
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (!function_exists('filled')) {
    /**
     * Determine if a value is "filled".
     *
     * @param mixed $value
     * @return bool
     */
    function is_filled(mixed $value)
    {
        return !is_blank($value);
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('transform')) {
    /**
     * Transform the given value if it is present.
     *
     * @param mixed $value
     * @param callable $callback
     * @param mixed $default
     * @return mixed|null
     */
    function transform(mixed $value, callable $callback, mixed $default = null)
    {
        if (is_filled($value)) {
            return $callback($value);
        }

        if (is_callable($default)) {
            return $default($value);
        }

        return $default;
    }
}

if (!function_exists('with')) {
    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @param mixed $value
     * @param callable|null $callback
     * @return mixed
     */
    function with(mixed $value, callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}