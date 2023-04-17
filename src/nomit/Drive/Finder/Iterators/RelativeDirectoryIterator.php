<?php

namespace nomit\Drive\Finder\Iterators;

use RecursiveArrayIterator;
use RecursiveDirectoryIterator;
use RecursiveIterator;
use RuntimeException;
use nomit\Drive\Finder\Exceptions\AccessDeniedError;
use nomit\Drive\Finder\Extensions\RelativeFileInfo;
use UnexpectedValueException;

/**
 * Class RelativeDirectoryIterator
 *
 * Extends the {@link \RecursiveDirectoryIterator} class to support relative paths.
 *
 * @author Victor Berchet <victor@suumit.com>
 */
class RelativeDirectoryIterator extends RecursiveDirectoryIterator
{

    /**
     * Ignore unreadable directory names/paths
     *
     * @var bool
     */
    private bool $ignore_unreadable_directories;

    /**
     * Whether the stream is rewindable
     *
     * @var bool
     */
    private $rewindable;

    // START OPTIMIZATION PROPERTIES
    //  The following  3 properties take part of the performance optimization to avoid unnecessarily redoing the same
    //  work in all iterations.

    /**
     * The directory root path
     *
     * @var string
     */
    private $root_path;

    /**
     * The current item sub-path
     *
     * @var string
     */
    private $sub_path;

    /**
     * The directory-seperating delimiter
     *
     * @var string
     */
    private string $directory_seperator = '/';

    // END OPTIMIZATION PROPERTIES

    /**
     * @param string $path The path of the directory
     * @param int $flags
     * @param bool $ignore_unreadable_directories
     */
    public function __construct(string $path, int $flags, bool $ignore_unreadable_directories = false)
    {
        if ($flags & (self::CURRENT_AS_PATHNAME | self::CURRENT_AS_SELF)) {
            throw new RuntimeException('This directory iterator only supports returning the currently pointed-to ' .
                'item as a {{RelativeFile}} (extension of {{SplFileInfo}}) instance.');
        }

        parent::__construct($path, $flags);

        $this->ignore_unreadable_directories = $ignore_unreadable_directories;
        $this->root_path = $path;

        if ('/' !== DIRECTORY_SEPARATOR && !($flags & self::UNIX_PATHS)) {
            $this->directory_seperator = DIRECTORY_SEPARATOR;
        }
    }

    /**
     * Return an instance of {@link RelativeFileInfo} with support for relative paths.
     *
     * @return RelativeFileInfo File information
     */
    public function current(): RelativeFileInfo
    {
        // The following logic avoids the unnecessary replication of work in all iterations
        if (null === $sub_path_name = $this->sub_path) {
            $sub_path_name = $this->sub_path = (string)$this->getSubPath();
        }

        if ('' !== $sub_path_name) {
            $sub_path_name .= $this->directory_seperator;
        }

        $sub_path_name .= $this->getFilename();

        if ('/' !== $base_path = $this->root_path) {
            $base_path .= $this->directory_seperator;
        }

        return new RelativeFileInfo($base_path . $sub_path_name, $this->sub_path, $sub_path_name);
    }

    /**
     * @return RecursiveIterator
     * @throws AccessDeniedError
     */
    public function getChildren(): RecursiveDirectoryIterator
    {
        try {
            $children = parent::getChildren();

            if ($children instanceof self) {
                // The parent method will call the constructor with default arguments, so that unreadable directories
                // won't be ignored anymore
                $children->ignore_unreadable_directories = $this->ignore_unreadable_directories;

                // Performance optimization to avoid unnecessarily redoing the same work in all children
                $children->rewindable = &$this->rewindable;
                $children->root_path = $this->root_path;
            }

            return $children;
        } catch (UnexpectedValueException $e) {
            if ($this->ignore_unreadable_directories) {
                // The directory is unreadable and the Finder has been set to ignore it: a fake empty content is returned.
                return new RecursiveArrayIterator([]);
            }

            throw new AccessDeniedError($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Do nothing for non-rewindable streams.
     */
    public function rewind(): void
    {
        if (false === $this->isRewindable()) {
            return;
        }

        parent::rewind();
    }

    /**
     * Checks if the stream is rewindable.
     *
     * @return bool True if the stream is rewindable, otherwise false.
     */
    public function isRewindable(): bool
    {
        if (null !== $this->rewindable) {
            return $this->rewindable;
        }

        if (false !== $stream = @opendir($this->getPath())) {
            $infos = stream_get_meta_data($stream);

            closedir($stream);

            if ($infos['seekable']) {
                return $this->rewindable = true;
            }
        }

        return $this->rewindable = false;
    }

}