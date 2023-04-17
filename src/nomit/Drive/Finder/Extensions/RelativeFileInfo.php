<?php

namespace nomit\Drive\Finder\Extensions;

use RuntimeException;
use SplFileInfo;
use const PATHINFO_FILENAME;

/**
 * Class RelativeFile
 *
 * Extends {@link \SplFileInfo} to support relative paths.
 *
 * Derived from Symfony.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/SplFileInfo.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright Copyright (C) 2004-2020 Fabien Potencier
 *
 * @package nomit\Filer;
 */
class RelativeFileInfo extends SplFileInfo
{

    /**
     * The file's relative path
     *
     * @see getRelativePath()
     *
     * @var string
     */
    private string $relative_path;

    /**
     * The file's relative pathname
     *
     * @see getRelativePathname()
     *
     * @var string
     */
    private string $relative_pathname;

    /**
     * @param string $file he file name
     * @param string $relative_path The relative path
     * @param string $relative_pathname The relative path name
     */
    public function __construct(string $file, string $relative_path, string $relative_pathname)
    {
        parent::__construct($file);

        $this->relative_path = $relative_path;
        $this->relative_pathname = $relative_pathname;
    }

    /**
     * Returns the relative path. This path DOES NOT contain the file name.
     *
     * @return string The relative path
     */
    public function getRelativePath(): string
    {
        return $this->relative_path;
    }

    /**
     * Returns the relative path name. This path DOES contain the file name.
     *
     * @return string the relative path name
     */
    public function getRelativePathname(): string
    {
        return $this->relative_pathname;
    }

    /**
     * Returns the file name WITHOUT its extension.
     *
     * @return string
     */
    public function getFilenameWithoutExtension(): string
    {
        $filename = $this->getFilename();

        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * Returns the contents of the file.
     *
     * @return string the contents of the file
     * @throws RuntimeException
     */
    public function getContents()
    {
        set_error_handler(function ($type, $msg) use (&$error) {
            $error = $msg;
        });

        $content = file_get_contents($this->getPathname());

        restore_error_handler();

        if (false === $content) {
            throw new RuntimeException($error);
        }

        return $content;
    }

}