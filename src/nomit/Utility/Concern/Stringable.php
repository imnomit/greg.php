<?php

namespace nomit\Utility\Concern;

/**
 * Interface Stringable
 *
 * @package nomit\Utilities\Concern
 */
interface Stringable
{

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function toString(): string;

    /**
     * @return string
     */
    public function __toString(): string;

}