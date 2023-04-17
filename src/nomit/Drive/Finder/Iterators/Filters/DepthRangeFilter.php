<?php

namespace nomit\Drive\Finder\Iterators\Filters;

use FilterIterator;
use RecursiveIteratorIterator;
use const PHP_INT_MAX;

/**
 * Class DepthRangeFilter
 *
 * Filters by limiting the allowed directory depth.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/DepthRangeFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class DepthRangeFilter extends FilterIterator
{

    /**
     * The minimum directory depth.
     *
     * @var int
     */
    private int $minimum_depth = 0;

    /**
     * @param RecursiveIteratorIterator $iterator The {@link Iterator} subject to be filtered
     * @param int $minimum_depth The minimum allowed directory depth
     * @param int $maximum_depth The maximum allowed directory depth
     */
    public function __construct(RecursiveIteratorIterator $iterator, int $minimum_depth = 0, int $maximum_depth = PHP_INT_MAX)
    {
        $this->minimum_depth = $minimum_depth;

        $iterator->setMaxDepth(PHP_INT_MAX === $maximum_depth ? -1 : $maximum_depth);

        parent::__construct($iterator);
    }

    /**
     * Filters the subject {@link Iterator} items.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        return $this->getInnerIterator()->getDepth() >= $this->minimum_depth;
    }

}