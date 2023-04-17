<?php

namespace nomit\Drive\Finder\Iterators;

use ArrayIterator;
use Closure;
use InvalidArgumentException;
use IteratorAggregate;
use SplFileInfo;
use Traversable;
use function is_callable;

/**
 * Class SortableIterator
 *
 * Applies a sort on a given {@link Iterator}.
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
 * @package nomit\HarDriver\Finder\Iterators
 */
class SortableIterator implements IteratorAggregate
{

    public const SORT_BY_NONE = 0;
    public const SORT_BY_NAME = 1;
    public const SORT_BY_TYPE = 2;
    public const SORT_BY_ACCESSED_TIME = 3;
    public const SORT_BY_CHANGED_TIME = 4;
    public const SORT_BY_MODIFIED_TIME = 5;
    public const SORT_BY_NAME_NATURAL = 6;

    /**
     * The {@link Iterator} to filter
     *
     * @var Traversable
     */
    private Traversable $iterator;

    /**
     * How to sort the subject
     *
     * @var int|Closure
     */
    private int|Closure $sort;

    /**
     * @param Traversable $iterator The Iterator to filter
     * @param int|callable $sort The sort type ({@link SORT_BY_NAME}, {@link SORT_BY_TYPE}, or a anonymous callback)
     * @param bool $reverse_order Whether to reverse the order of the sorting
     */
    public function __construct(Traversable $iterator, int|callable $sort, bool $reverse_order = false)
    {
        $this->iterator = $iterator;
        $order = $reverse_order ? -1 : 1;

        if (self::SORT_BY_NAME === $sort) {
            $this->sort = static function (SplFileInfo $a, SplFileInfo $b) use ($order) {
                return $order * strcmp($a->getRealPath() ?: $a->getPathname(),
                        $b->getRealPath() ?: $b->getPathname());
            };
        } else if (self::SORT_BY_NAME_NATURAL === $sort) {
            $this->sort = static function (SplFileInfo $a, SplFileInfo $b) use ($order) {
                return $order * strnatcmp($a->getRealPath() ?: $a->getPathname(),
                        $b->getRealPath() ?: $b->getPathname());
            };
        } else if (self::SORT_BY_TYPE === $sort) {
            $this->sort = static function (SplFileInfo $a, SplFileInfo $b) use ($order) {
                if ($a->isDir() && $b->isFile()) {
                    return -$order;
                }

                if ($a->isFile() && $b->isDir()) {
                    return $order;
                }

                return $order * strcmp($a->getRealPath() ?: $a->getPathname(),
                        $b->getRealPath() ?: $b->getPathname());
            };
        } else if (self::SORT_BY_ACCESSED_TIME === $sort) {
            $this->sort = static function (SplFileInfo $a, SplFileInfo $b) use ($order) {
                return $order * ($a->getATime() - $b->getATime());
            };
        } else if (self::SORT_BY_CHANGED_TIME === $sort) {
            $this->sort = static function (SplFileInfo $a, SplFileInfo $b) use ($order) {
                return $order * ($a->getCTime() - $b->getCTime());
            };
        } else if (self::SORT_BY_MODIFIED_TIME === $sort) {
            $this->sort = static function (SplFileInfo $a, SplFileInfo $b) use ($order) {
                return $order * ($a->getMTime() - $b->getMTime());
            };
        } else if (self::SORT_BY_NONE === $sort) {
            $this->sort = $order;
        } else if (is_callable($sort)) {
            $this->sort = $reverse_order
                ? static function (SplFileInfo $a, SplFileInfo $b) use ($sort) {
                    return -$sort($a, $b);
                }
                : $sort;
        } else {
            throw new InvalidArgumentException('The supplied sort-by parameter is neither a anonymous callback ' .
                'nor a recognized sort-by algorithm flag, and is thus invalid.');
        }
    }

    /**
     * @return Traversable
     */
    public function getIterator(): \Traversable
    {
        if (1 === $this->sort) {
            return $this->iterator;
        }

        $array = iterator_to_array($this->iterator, true);

        if (-1 === $this->sort) {
            $array = array_reverse($array);
        } else {
            uasort($array, $this->sort);
        }

        return new ArrayIterator($array);
    }

}