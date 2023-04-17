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
use nomit\Image\Image\Palette\RGB;

/**
 * A filter to render web-optimized images.
 */
class WebOptimization implements FilterInterface
{
    /**
     * @var \nomit\Image\Image\Palette\RGB
     */
    private $palette;

    /**
     * @var string|callable|null
     */
    private $path;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string|callable|null $path
     * @param array $options
     */
    public function __construct($path = null, array $options = array())
    {
        $this->path = $path;
        $this->options = array_replace(array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-y' => 72,
            'resolution-x' => 72,
        ), $options);
        $this->palette = new RGB();
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        $image
            ->usePalette($this->palette)
            ->strip();

        if (is_callable($this->path)) {
            $path = call_user_func($this->path, $image);
        } elseif ($this->path !== null) {
            $path = $this->path;
        } else {
            return $image;
        }

        return $image->save($path, $this->options);
    }
}
