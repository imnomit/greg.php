<?php

namespace nomit\Drive\Finder\Iterators\Filters;

use FilterIterator;
use InvalidArgumentException;
use Iterator;
use function is_callable;

/**
 * Class CustomFilter
 *
 * Filters files by applying set anonymous functions. The anonymous function receives a {@link \SplFileInfo} instance
 *  and must return false for a file to be removed (i.e. filtered) from the list.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/CustomFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class CustomFilter extends FilterIterator
{

    /**
     * @var array|callable[]
     */
    private array $filters = [];

    /**
     * @param Iterator $iterator The Iterator (i.e. subject) to filter
     * @param callable[] $filters An array of callbacks to be applied against the subject as filters
     * @throws InvalidArgumentException
     */
    public function __construct(Iterator $iterator, array $filters)
    {
        foreach ($filters as $filter) {
            if (!is_callable($filter)) {
                throw new InvalidArgumentException('The supplied filter callback is uncallable, and thus invalid.');
            }
        }

        $this->filters = $filters;

        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        $file_info = $this->current();

        foreach ($this->filters as $filter) {
            if (false === $filter($file_info)) {
                return false;
            }
        }

        return true;
    }

}