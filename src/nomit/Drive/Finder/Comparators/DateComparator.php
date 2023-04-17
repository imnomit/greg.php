<?php

namespace nomit\Drive\Finder\Comparators;

use DateTime;
use Exception;
use InvalidArgumentException;

/**
 * Class DateComparator
 *
 * Compiles date comparisons.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Comparator/DateComparator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Filer\Comparators
 */
class DateComparator extends Comparator
{

    /**
     * @param string $test A comparison string
     * @throws InvalidArgumentException If the test is not understood
     */
    public function __construct(string $test)
    {
        if (!preg_match('#^\s*(==|!=|[<>]=?|after|since|before|until)?\s*(.+?)\s*$#i', $test, $matches)) {
            throw new InvalidArgumentException('The supplied test value, {{' . $test . '}}, could not be ' .
                'understood as a date test, and is thus invalid.');
        }

        try {
            $date = new DateTime($matches[2]);
            $target = $date->format('U');
        } catch (Exception $e) {
            throw new InvalidArgumentException('The supplied test value, {{' . $test . '}}, is not a ' .
                'validly-formatted date.');
        }

        $operator = $matches[1] ?? '==';

        if ('since' === $operator || 'after' === $operator) {
            $operator = '>';
        }

        if ('until' === $operator || 'before' === $operator) {
            $operator = '<';
        }

        $this->setOperator($operator);
        $this->setTarget($target);
    }

}