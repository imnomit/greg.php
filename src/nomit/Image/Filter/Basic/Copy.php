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
 * A copy filter.
 */
class Copy implements FilterInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->copy();
    }
}
