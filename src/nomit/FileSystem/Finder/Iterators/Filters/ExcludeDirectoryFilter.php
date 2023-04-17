<?php

namespace nomit\FileSystem\Finder\Iterators\Filters;

use FilterIterator;
use Iterator;
use RecursiveIterator;

/**
 * Class ExcludeDirectoryFilter
 *
 * Filters from a subject those directories that match those listed for exclusion herein.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/ExcludeDirectoryFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class ExcludeDirectoryFilter extends FilterIterator implements RecursiveIterator
{

    /**
     * The subject {@link Iterator} to be filtered
     *
     * @var Iterator
     */
    private Iterator $iterator;

    /**
     * Whether the current {@see $iterator} is a {@link RecursiveIterator}
     *
     * @var bool
     */
    private bool $is_recursive;

    /**
     * Directory names/paths that when matched will be filtered out
     *
     * @var array
     */
    private array $excluded_directories = [];

    /**
     * The RegEx pattern to be used in filtering those directories listed in {@see $excluded_directories}
     *
     * @var string
     */
    private $exclude_pattern;

    /**
     * @param Iterator $iterator The Iterator to filter
     * @param string[] $directories An array of directories to exclude
     */
    public function __construct(Iterator $iterator, array $directories)
    {
        $this->iterator = $iterator;
        $this->is_recursive = $iterator instanceof RecursiveIterator;

        $patterns = [];

        foreach ($directories as $directory) {
            $directory = rtrim($directory, '/');

            if (!$this->is_recursive || str_contains($directory, '/')) {
                $patterns[] = preg_quote($directory, '#');
            } else {
                $this->excluded_directories[$directory] = true;
            }
        }

        if ($patterns) {
            $this->exclude_pattern = '#(?:^|/)(?:' . implode('|', $patterns) . ')(?:/|$)#';
        }

        parent::__construct($iterator);
    }

    /**
     * Filters the subject {@link Iterator} items.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        if ($this->is_recursive && isset($this->excluded_directories[$this->getFilename()]) && $this->isDir()) {
            return false;
        }

        if ($this->exclude_pattern) {
            $path = $this->isDir() ? $this->current()->getRelativePathname() : $this->current()->getRelativePath();
            $path = str_replace('\\', '/', $path);

            return !preg_match($this->exclude_pattern, $path);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->is_recursive && $this->iterator->hasChildren();
    }

    /**
     * @return ExcludeDirectoryFilter
     */
    public function getChildren(): ?RecursiveIterator
    {
        $children = new self($this->iterator->getChildren(), []);
        $children->excluded_directories = $this->excluded_directories;
        $children->exclude_pattern = $this->exclude_pattern;

        return $children;
    }

}