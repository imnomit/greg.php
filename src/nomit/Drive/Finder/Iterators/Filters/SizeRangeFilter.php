<?php

namespace nomit\Drive\Finder\Iterators\Filters;

use FilterIterator;
use Iterator;
use nomit\Drive\Finder\Comparators\NumberComparator;

/**
 * Class SizeRangeFilter
 *
 * Filters out files that are not in the given size range.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/SizeRangeFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class SizeRangeFilter extends FilterIterator
{

    /**
     * Operators to use when comparing supplied size-ranges.
     *
     * @var NumberComparator[]
     */
    private array $comparators;

    /**
     * @param Iterator $iterator The Iterator to filter
     * @param NumberComparator[] $comparators An array of {@link NumberComparator} instances
     */
    public function __construct(Iterator $iterator, array $comparators)
    {
        $this->comparators = $comparators;

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

        if (!$file_info->isFile()) {
            return true;
        }

        $file_size = $file_info->getSize();

        foreach ($this->comparators as $compare) {
            if (!$compare->test($file_size)) {
                return false;
            }
        }

        return true;
    }
}