<?php

namespace nomit\Drive\Finder\Iterators\Filters;

use FilterIterator;
use Iterator;

/**
 * Class FileTypeFilter
 *
 * Filters by keeping only files, directories, or both.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/FileTypeFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class FileTypeFilter extends FilterIterator
{

    public const ONLY_FILES = 1;
    public const ONLY_DIRECTORIES = 2;

    /**
     * The mode for which type (i.e. file, directory) of item to keep
     *
     * @var int
     */
    private int $mode;

    /**
     * @param Iterator $iterator The Iterator to filter
     * @param int $mode The mode (self::ONLY_FILES or self::ONLY_DIRECTORIES)
     */
    public function __construct(Iterator $iterator, int $mode)
    {
        $this->mode = $mode;

        parent::__construct($iterator);
    }

    /**
     * Filters the subject {@link Iterator} items.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        $file_info = $this->current();

        if (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $file_info->isFile()) {
            return false;
        }

        if (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $file_info->isDir()) {
            return false;
        }

        return true;
    }
}