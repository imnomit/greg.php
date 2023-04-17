<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Image\Image\Fill\Gradient;

use nomit\Image\Image\Fill\FillInterface;
use nomit\Image\Image\Palette\Color\ColorInterface;
use nomit\Image\Image\PointInterface;

/**
 * Linear gradient fill.
 */
abstract class Linear implements FillInterface
{
    /**
     * @var int
     */
    private $length;

    /**
     * @var \nomit\Image\Image\Palette\Color\ColorInterface
     */
    private $start;

    /**
     * @var \nomit\Image\Image\Palette\Color\ColorInterface
     */
    private $end;

    /**
     * Constructs a linear gradient with overall gradient length, and start and
     * end shades, which default to 0 and 255 accordingly.
     *
     * @param int $length
     * @param \nomit\Image\Image\Palette\Color\ColorInterface $start
     * @param \nomit\Image\Image\Palette\Color\ColorInterface $end
     */
    final public function __construct($length, ColorInterface $start, ColorInterface $end)
    {
        $this->length = $length;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\Fill\FillInterface::getColor()
     */
    final public function getColor(PointInterface $position)
    {
        $l = $this->getDistance($position);

        if ($l >= $this->length) {
            return $this->end;
        }

        if ($l < 0) {
            return $this->start;
        }

        return $this->start->getPalette()->blend($this->start, $this->end, $l / $this->length);
    }

    /**
     * @return \nomit\Image\Image\Palette\Color\ColorInterface
     */
    final public function getStart()
    {
        return $this->start;
    }

    /**
     * @return \nomit\Image\Image\Palette\Color\ColorInterface
     */
    final public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get the distance of the position relative to the beginning of the gradient.
     *
     * @param \nomit\Image\Image\PointInterface $position
     *
     * @return int
     */
    abstract protected function getDistance(PointInterface $position);
}
