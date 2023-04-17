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
use nomit\Image\Image\Palette\Color\ColorInterface;

/**
 * A rotate filter.
 */
class Rotate implements FilterInterface
{
    /**
     * @var int
     */
    private $angle;

    /**
     * @var \nomit\Image\Image\Palette\Color\ColorInterface
     */
    private $background;

    /**
     * Constructs Rotate filter with given angle and background color.
     *
     * @param int $angle
     * @param \nomit\Image\Image\Palette\Color\ColorInterface $background
     */
    public function __construct($angle, ColorInterface $background = null)
    {
        $this->angle = $angle;
        $this->background = $background;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return $image->rotate($this->angle, $this->background);
    }
}
