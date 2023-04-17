<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Image\Image;

use nomit\Image\Factory\ClassFactory;
use nomit\Image\Factory\ClassFactoryAwareInterface;
use nomit\Image\Factory\ClassFactoryInterface;

abstract class AbstractLayers implements LayersInterface, ClassFactoryAwareInterface
{
    /**
     * @var \nomit\Image\Factory\ClassFactoryInterface|null
     */
    private $classFactory;

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::add()
     */
    public function add(ImageInterface $image)
    {
        $this[] = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::set()
     */
    public function set($offset, ImageInterface $image)
    {
        $this[$offset] = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::remove()
     */
    public function remove($offset)
    {
        unset($this[$offset]);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::get()
     */
    public function get($offset)
    {
        return $this[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Image\LayersInterface::has()
     */
    public function has($offset)
    {
        return isset($this[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryAwareInterface::setClassFactory()
     */
    public function setClassFactory(ClassFactoryInterface $classFactory)
    {
        $this->classFactory = $classFactory;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryAwareInterface::getClassFactory()
     */
    public function getClassFactory()
    {
        if ($this->classFactory === null) {
            $this->classFactory = new ClassFactory();
        }

        return $this->classFactory;
    }
}
