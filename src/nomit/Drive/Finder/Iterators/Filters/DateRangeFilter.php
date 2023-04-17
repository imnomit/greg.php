<?php

namespace nomit\Drive\Finder\Iterators\Filters;

use FilterIterator;
use Iterator;
use nomit\Drive\Finder\Comparators\DateComparator;

/**
 * Class DateRangeFilter
 *
 * Filters out files with last-modified values that are not in the given date range.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/DateRangeFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class DateRangeFilter extends FilterIterator
{

    /**
     * Stores the testing {@link DateComparator} instances.
     *
     * @var DateComparator[]
     */
    private array $comparators;

    /**
     * @param Iterator $iterator The {@link Iterator} to filter
     * @param DateComparator[] $comparators An array of {@link DateComparator} comparator instances
     */
    public function __construct(Iterator $iterator, array $comparators)
    {
        $this->comparators = $comparators;

        parent::__construct($iterator);
    }

    /**
     * Filters the {@link Iterator} values against the {@link DateComparator} comparators.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        $file_info = $this->current();

        if (!file_exists($file_info->getPathname())) {
            return false;
        }

        $file_date = $file_info->getMTime();

        foreach ($this->comparators as $compare) {
            if (!$compare->test($file_date)) {
                return false;
            }
        }

        return true;
    }

}