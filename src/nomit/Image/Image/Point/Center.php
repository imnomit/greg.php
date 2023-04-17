<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Image\Image\Point;

use nomit\Image\Image\BoxInterface;
use nomit\Image\Image\Point as OriginalPoint;
use nomit\Image\Image\PointInterface;

/**
 * Center point of a box.
 */
final class Center implements PointInterface
{
    /**
     * @var \nomit\Image\Image\BoxInterface
     */
    private $box;

    /**
     * Constructs coordinate with size instance, it needs to be relative to.
     *
     * @param \nomit\Image\Image\BoxInterface $box
     */
    public function __construct(BoxInterface $box)
    {
        $this->box = $box;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\PointInterface::getX()
     */
    public function getX()
    {
        return ceil($this->box->getWidth() / 2);
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\PointInterface::getY()
     */
    public function getY()
    {
        return ceil($this->box->getHeight() / 2);
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\PointInterface::in()
     */
    public function in(BoxInterface $box)
    {
        return $this->getX() < $box->getWidth() && $this->getY() < $box->getHeight();
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\PointInterface::move()
     */
    public function move($amount)
    {
        return new OriginalPoint($this->getX() + $amount, $this->getY() + $amount);
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\PointInterface::__toString()
     */
    public function __toString()
    {
        return sprintf('(%d, %d)', $this->getX(), $this->getY());
    }
}
