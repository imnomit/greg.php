<?php

namespace nomit\Image\Factory;

use nomit\Image\Exception\InvalidArgumentException;
use nomit\Image\Exception\RuntimeException;
use nomit\Image\File\Loader;
use nomit\Image\Image\Box;
use nomit\Image\Image\ImageInterface;
use nomit\Image\Image\Metadata\DefaultMetadataReader;
use nomit\Image\Image\Metadata\ExifMetadataReader;
use nomit\Image\Image\Metadata\MetadataBag;
use nomit\Image\Image\Palette\Color\ColorInterface;
use nomit\Image\Image\Palette\PaletteInterface;

/**
 * The default implementation of nomit\Image\Factory\ClassFactoryInterface.
 */
class ClassFactory implements ClassFactoryInterface
{
    /**
     * @var array|null
     */
    private static $gdInfo;

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createMetadataReader()
     */
    public function createMetadataReader()
    {
        return $this->finalize(ExifMetadataReader::isSupported() ? new ExifMetadataReader() : new DefaultMetadataReader());
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createBox()
     */
    public function createBox($width, $height)
    {
        return $this->finalize(new Box($width, $height));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createFileLoader()
     */
    public function createFileLoader($path)
    {
        return $this->finalize(new Loader($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createDrawer()
     */
    public function createDrawer($handle, $resource)
    {
        switch ($handle) {
            case self::HANDLE_GD:
                return $this->finalize(new \nomit\Image\Gd\Drawer($resource));
            case self::HANDLE_GMAGICK:
                return $this->finalize(new \nomit\Image\Gmagick\Drawer($resource));
            case self::HANDLE_IMAGICK:
                return $this->finalize(new \nomit\Image\Imagick\Drawer($resource));
            default:
                throw new InvalidArgumentException(sprintf('Unrecognized handle %s in %s', $handle, __FUNCTION__));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createLayers()
     */
    public function createLayers($handle, ImageInterface $image, $initialKey = null)
    {
        switch ($handle) {
            case self::HANDLE_GD:
                return $this->finalize(new \nomit\Image\Gd\Layers($image, $image->palette(), $image->getGdResource(), (int) $initialKey));
            case self::HANDLE_GMAGICK:
                return $this->finalize(new \nomit\Image\Gmagick\Layers($image, $image->palette(), $image->getGmagick(), (int) $initialKey));
            case self::HANDLE_IMAGICK:
                return $this->finalize(new \nomit\Image\Imagick\Layers($image, $image->palette(), $image->getImagick(), (int) $initialKey));
            default:
                throw new InvalidArgumentException(sprintf('Unrecognized handle %s in %s', $handle, __FUNCTION__));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createEffects()
     */
    public function createEffects($handle, $resource)
    {
        switch ($handle) {
            case self::HANDLE_GD:
                return $this->finalize(new \nomit\Image\Gd\Effects($resource));
            case self::HANDLE_GMAGICK:
                return $this->finalize(new \nomit\Image\Gmagick\Effects($resource));
            case self::HANDLE_IMAGICK:
                return $this->finalize(new \nomit\Image\Imagick\Effects($resource));
            default:
                throw new InvalidArgumentException(sprintf('Unrecognized handle %s in %s', $handle, __FUNCTION__));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createImage()
     */
    public function createImage($handle, $resource, PaletteInterface $palette, MetadataBag $metadata)
    {
        switch ($handle) {
            case self::HANDLE_GD:
                return $this->finalize(new \nomit\Image\Gd\Image($resource, $palette, $metadata));
            case self::HANDLE_GMAGICK:
                return $this->finalize(new \nomit\Image\Gmagick\Imagine($resource, $palette, $metadata));
            case self::HANDLE_IMAGICK:
                return $this->finalize(new \nomit\Image\Imagick\Image($resource, $palette, $metadata));
            default:
                throw new InvalidArgumentException(sprintf('Unrecognized handle %s in %s', $handle, __FUNCTION__));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \nomit\Image\Factory\ClassFactoryInterface::createFont()
     */
    public function createFont($handle, $file, $size, ColorInterface $color)
    {
        switch ($handle) {
            case self::HANDLE_GD:
                $gdInfo = static::getGDInfo();
                if (!$gdInfo['FreeType Support']) {
                    throw new RuntimeException('GD is not compiled with FreeType support');
                }

                return $this->finalize(new \nomit\Image\Gd\Font($file, $size, $color));
            case self::HANDLE_GMAGICK:
                $gmagick = new \Gmagick();
                $gmagick->newimage(1, 1, 'transparent');

                return $this->finalize(new \nomit\Image\Gmagick\Font($gmagick, $file, $size, $color));
            case self::HANDLE_IMAGICK:
                return $this->finalize(new \nomit\Image\Imagick\Font(new \Imagick(), $file, $size, $color));
            default:
                throw new InvalidArgumentException(sprintf('Unrecognized handle %s in %s', $handle, __FUNCTION__));
        }
    }

    /**
     * Finalize the newly created object.
     *
     * @param object $object
     *
     * @return object
     */
    protected function finalize($object)
    {
        if ($object instanceof ClassFactoryAwareInterface) {
            $object->setClassFactory($this);
        }

        return $object;
    }

    /**
     * @return array
     */
    protected static function getGDInfo()
    {
        if (self::$gdInfo === null) {
            if (!function_exists('gd_info')) {
                throw new RuntimeException('GD is not installed');
            }
            self::$gdInfo = gd_info();
        }

        return self::$gdInfo;
    }
}
