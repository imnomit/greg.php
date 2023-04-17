<?php

namespace nomit\Resource\Resource\UploadedFile\Destination;

use nomit\FileSystem\Directory\Directory;
use nomit\FileSystem\Directory\DirectoryAwareTrait;
use nomit\FileSystem\Directory\DirectoryInterface;

/**
 * Class Destination
 * @package nomit\Resource\resource\UploadedFile
 */
class Destination implements DestinationInterface
{

    use DirectoryAwareTrait;

    /**
     * @param string $pathName
     * @return static
     */
    public static function factoryFromPathName(string $pathName): self
    {
        return new self(new Directory($pathName));
    }

    /**
     * @param DirectoryInterface $directory
     * @return static
     */
    public static function factory(DirectoryInterface $directory): self
    {
        return new self($directory);
    }


    /**
     * Destination constructor.
     * @param DirectoryInterface $directory
     */
    public function __construct(DirectoryInterface $directory)
    {
        $this->setDirectory($directory);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function getFilePathName(string $fileName): string
    {
        return $this->directory->getPathname() . DIRECTORY_SEPARATOR . $fileName;
    }

}