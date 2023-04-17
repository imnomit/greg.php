<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Image\Image\Histogram;

/**
 * Bucket histogram.
 */
final class Bucket implements \Countable
{
    /**
     * @var \nomit\Image\Image\Histogram\Range
     */
    private $range;

    /**
     * @var int
     */
    private $count;

    /**
     * @param \nomit\Image\Image\Histogram\Range $range
     * @param int $count
     */
    public function __construct(Range $range, $count = 0)
    {
        $this->range = $range;
        $this->count = $count;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function add($value)
    {
        if ($this->range->contains($value)) {
            $this->count++;
        }

        return $this;
    }

    /**
     * @return int the number of elements in the bucket
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->count;
    }
}
