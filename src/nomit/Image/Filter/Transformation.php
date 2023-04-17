<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Image\Filter;

use nomit\Image\Exception\InvalidArgumentException;
use nomit\Image\Filter\Basic\ApplyMask;
use nomit\Image\Filter\Basic\Copy;
use nomit\Image\Filter\Basic\Crop;
use nomit\Image\Filter\Basic\Fill;
use nomit\Image\Filter\Basic\FlipHorizontally;
use nomit\Image\Filter\Basic\FlipVertically;
use nomit\Image\Filter\Basic\Paste;
use nomit\Image\Filter\Basic\Resize;
use nomit\Image\Filter\Basic\Rotate;
use nomit\Image\Filter\Basic\Save;
use nomit\Image\Filter\Basic\Show;
use nomit\Image\Filter\Basic\Strip;
use nomit\Image\Filter\Basic\Thumbnail;
use nomit\Image\Image\BoxInterface;
use nomit\Image\Image\Fill\FillInterface;
use nomit\Image\Image\ImageInterface;
use nomit\Image\Image\ImagineInterface;
use nomit\Image\Image\ManipulatorInterface;
use nomit\Image\Image\Palette\Color\ColorInterface;
use nomit\Image\Image\PointInterface;

/**
 * A transformation filter.
 */
final class Transformation implements FilterInterface, ManipulatorInterface
{
    /**
     * @var array[\nomit\Image\Filter\FilterInterface[]]
     */
    private $filters = array();

    /**
     * @var array[\nomit\Image\Filter\FilterInterface[]]|null
     */
    private $sorted;

    /**
     * An ImagineInterface instance.
     *
     * @var \nomit\Image\Image\ImageInterface|null
     */
    private $imagine;

    /**
     * Class constructor.
     *
     * @param \nomit\Image\Image\ImageInterface|null $imagine An ImagineInterface instance
     */
    public function __construct(ImagineInterface $imagine = null)
    {
        $this->imagine = $imagine;
    }

    /**
     * Applies a given FilterInterface onto given ImageInterface and returns modified ImageInterface.
     *
     * @param \nomit\Image\Image\ImageInterface $image
     * @param \nomit\Image\Filter\FilterInterface $filter
     *
     * @throws \nomit\Image\Exception\InvalidArgumentException
     *
     * @return \nomit\Image\Image\ImageInterface
     */
    public function applyFilter(ImageInterface $image, FilterInterface $filter)
    {
        if ($filter instanceof ImagineAware) {
            if ($this->imagine === null) {
                throw new InvalidArgumentException(sprintf('In order to use %s pass an nomit\Image\Image\ImagineInterface instance to Transformation constructor', get_class($filter)));
            }
            $filter->setImagine($this->imagine);
        }

        return $filter->apply($image);
    }

    /**
     * Returns a list of filters sorted by their priority. Filters with same priority will be returned in the order they were added.
     *
     * @return array
     */
    public function getFilters()
    {
        if ($this->sorted === null) {
            if (!empty($this->filters)) {
                ksort($this->filters);
                $this->sorted = call_user_func_array('array_merge', $this->filters);
            } else {
                $this->sorted = array();
            }
        }

        return $this->sorted;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Filter\FilterInterface::apply()
     */
    public function apply(ImageInterface $image)
    {
        return array_reduce(
            $this->getFilters(),
            array($this, 'applyFilter'),
            $image
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::copy()
     */
    public function copy()
    {
        return $this->add(new Copy());
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::crop()
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        return $this->add(new Crop($start, $size));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::flipHorizontally()
     */
    public function flipHorizontally()
    {
        return $this->add(new FlipHorizontally());
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::flipVertically()
     */
    public function flipVertically()
    {
        return $this->add(new FlipVertically());
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::strip()
     */
    public function strip()
    {
        return $this->add(new Strip());
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::paste()
     */
    public function paste(ImageInterface $image, PointInterface $start, $alpha = 100)
    {
        return $this->add(new Paste($image, $start, $alpha));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::applyMask()
     */
    public function applyMask(ImageInterface $mask)
    {
        return $this->add(new ApplyMask($mask));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::fill()
     */
    public function fill(FillInterface $fill)
    {
        return $this->add(new Fill($fill));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::resize()
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        return $this->add(new Resize($size, $filter));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::rotate()
     */
    public function rotate($angle, ColorInterface $background = null)
    {
        return $this->add(new Rotate($angle, $background));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::save()
     */
    public function save($path = null, array $options = array())
    {
        return $this->add(new Save($path, $options));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::show()
     */
    public function show($format, array $options = array())
    {
        return $this->add(new Show($format, $options));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\ManipulatorInterface::thumbnail()
     */
    public function thumbnail(BoxInterface $size, $settings = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        return $this->add(new Thumbnail($size, $settings, $filter));
    }

    /**
     * Registers a given FilterInterface in an internal array of filters for later application to an instance of ImageInterface.
     *
     * @param \nomit\Image\Filter\FilterInterface $filter
     * @param int $priority
     *
     * @return $this
     */
    public function add(FilterInterface $filter, $priority = 0)
    {
        $this->filters[$priority][] = $filter;
        $this->sorted = null;

        return $this;
    }
}
