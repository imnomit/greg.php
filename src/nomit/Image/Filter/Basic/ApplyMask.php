<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Image\Filter\Basic;

use nomit\Image\Filter\FilterInterface;
use nomit\Image\Image\ImageInterface;

/**
 * An apply mask filter.
 */
class ApplyMask implements FilterInterface
{
    /**
     * @var \nomit\Image\Image\ImageInterface
     */
    private $mask;

    /**
     * Initialize the instance.
     *
     * @param \nomit\Image\Image\ImageInterface $mask
     */
    public function __construct(ImageInterface $mask)
    {
        $this->mask = $mask;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->applyMask($this->mask);
    }
}
