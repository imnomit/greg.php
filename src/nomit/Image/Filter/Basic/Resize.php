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
use nomit\Image\Image\BoxInterface;
use nomit\Image\Image\ImageInterface;

/**
 * A resize filter.
 */
class Resize implements FilterInterface
{
    /**
     * @var \nomit\Image\Image\BoxInterface
     */
    private $size;

    /**
     * @var string
     */
    private $filter;

    /**
     * Constructs Resize filter with given width and height.
     *
     * @param \nomit\Image\Image\BoxInterface $size
     * @param string $filter
     */
    public function __construct(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->size = $size;
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->resize($this->size, $this->filter);
    }
}
