<?php

namespace nomit\Utility\Tap;

/**
 * Class Proxy
 *
 * Derived from Laravel.
 *
 * See original documentation:
 * @see https://laravel.com/api/5.5/Illuminate/Support/HigherOrderTapProxy.html
 *
 * See original code, from which this is derived:
 * @see https://github.com/mfn/laravel-framework/blob/master/src/Illuminate/Support/HigherOrderTapProxy.php
 *
 * @package nomit\Utilities\Tap
 */
class Proxy
{

    /**
     * The target being tapped.
     *
     * @var mixed
     */
    public mixed $target = null;

    /**
     * Create a new tap proxy instance.
     *
     * @param mixed $target
     */
    public function __construct(mixed $target)
    {
        $this->target = $target;
    }

    /**
     * Dynamically pass method calls to the target.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        $this->target->{$method}(...$parameters);

        return $this->target;
    }

}