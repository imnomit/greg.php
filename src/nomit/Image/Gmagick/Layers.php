<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Image\Gmagick;

use nomit\Image\Driver\InfoProvider;
use nomit\Image\Exception\InvalidArgumentException;
use nomit\Image\Exception\OutOfBoundsException;
use nomit\Image\Exception\RuntimeException;
use nomit\Image\Factory\ClassFactoryInterface;
use nomit\Image\Image\AbstractLayers;
use nomit\Image\Image\Format;
use nomit\Image\Image\Metadata\MetadataBag;
use nomit\Image\Image\Palette\PaletteInterface;

class Layers extends AbstractLayers implements InfoProvider
{
    /**
     * @var \nomit\Image\Gmagick\Imagine
     */
    private $image;

    /**
     * @var \Gmagick
     */
    private $resource;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var \nomit\Image\Gmagick\Imagine[]
     */
    private $layers = array();

    /**
     * @var \nomit\Image\Image\Palette\PaletteInterface
     */
    private $palette;

    /**
     * @param \nomit\Image\Gmagick\Imagine $image
     * @param \nomit\Image\Image\Palette\PaletteInterface $palette
     * @param \Gmagick $resource
     * @param int $initialOffset
     */
    public function __construct(Imagine $image, PaletteInterface $palette, \Gmagick $resource, $initialOffset = 0)
    {
        $this->image = $image;
        $this->resource = $resource;
        $this->palette = $palette;
        $this->offset = (int) $initialOffset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Driver\InfoProvider::getDriverInfo()
     * @since 1.3.0
     */
    public static function getDriverInfo($required = true)
    {
        return DriverInfo::get($required);
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::merge()
     */
    public function merge()
    {
        foreach ($this->layers as $offset => $image) {
            try {
                $this->resource->setimageindex($offset);
                $this->resource->setimage($image->getGmagick());
            } catch (\GmagickException $e) {
                throw new RuntimeException('Failed to substitute layer', $e->getCode(), $e);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::coalesce()
     */
    public function coalesce()
    {
        static::getDriverInfo()->requireFeature(DriverInfo::FEATURE_COALESCELAYERS);
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::animate()
     */
    public function animate($format, $delay, $loops)
    {
        $formatInfo = Format::get($format);
        if ($formatInfo === null || $formatInfo->getID() !== Format::ID_GIF) {
            throw new InvalidArgumentException('Animated picture is currently only supported on gif');
        }

        if (!is_int($loops) || $loops < 0) {
            throw new InvalidArgumentException('Loops must be a positive integer.');
        }

        if ($delay !== null && (!is_int($delay) || $delay < 0)) {
            throw new InvalidArgumentException('Delay must be either null or a positive integer.');
        }

        try {
            for ($offset = 0; $offset < $this->count(); $offset++) {
                $this->resource->setimageindex($offset);
                $this->resource->setimageformat($format);

                if ($delay !== null) {
                    $this->resource->setimagedelay($delay / 10);
                }

                $this->resource->setimageiterations($loops);
            }
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to animate layers', $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::current()
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->extractAt($this->offset);
    }

    /**
     * Tries to extract layer at given offset.
     *
     * @param int $offset
     *
     * @return \nomit\Image\Gmagick\Imagine
     *@throws \nomit\Image\Exception\RuntimeException
     *
     */
    private function extractAt($offset)
    {
        if (!isset($this->layers[$offset])) {
            try {
                $this->resource->setimageindex($offset);
                $this->layers[$offset] = $this->getClassFactory()->createImage(ClassFactoryInterface::HANDLE_GMAGICK, $this->resource->getimage(), $this->palette, new MetadataBag());
            } catch (\GmagickException $e) {
                throw new RuntimeException(sprintf('Failed to extract layer %d', $offset), $e->getCode(), $e);
            }
        }

        return $this->layers[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::key()
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::next()
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        ++$this->offset;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::rewind()
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::valid()
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->offset < count($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Countable::count()
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        try {
            return $this->resource->getnumberimages();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Failed to count the number of layers', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetExists()
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return is_int($offset) && $offset >= 0 && $offset < count($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetGet()
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->extractAt($offset);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetSet()
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $image)
    {
        if (!$image instanceof Imagine) {
            throw new InvalidArgumentException('Only a Gmagick Image can be used as layer');
        }

        if ($offset === null) {
            $offset = count($this) - 1;
        } else {
            if (!is_int($offset)) {
                throw new InvalidArgumentException('Invalid offset for layer, it must be an integer');
            }

            if (count($this) < $offset || $offset < 0) {
                throw new OutOfBoundsException(sprintf('Invalid offset for layer, it must be a value between 0 and %d, %d given', count($this), $offset));
            }

            if (isset($this[$offset])) {
                unset($this[$offset]);
                $offset = $offset - 1;
            }
        }

        $frame = $image->getGmagick();

        try {
            if (count($this) > 0) {
                $this->resource->setimageindex($offset);
                $this->resource->nextimage();
            }
            $this->resource->addimage($frame);

            // ugly hack to bypass issue https://bugs.php.net/bug.php?id=64623
            if (count($this) == 2) {
                $this->resource->setimageindex($offset + 1);
                $this->resource->nextimage();
                $this->resource->addimage($frame);
                unset($this[0]);
            }
        } catch (\GmagickException $e) {
            throw new RuntimeException('Unable to set the layer', $e->getCode(), $e);
        }

        $this->layers = array();
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetUnset()
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        try {
            $this->extractAt($offset);
        } catch (RuntimeException $e) {
            return;
        }

        try {
            $this->resource->setimageindex($offset);
            $this->resource->removeimage();
        } catch (\GmagickException $e) {
            throw new RuntimeException('Unable to remove layer', $e->getCode(), $e);
        }
    }
}
