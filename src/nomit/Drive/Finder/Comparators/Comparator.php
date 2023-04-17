<?php

namespace nomit\Drive\Finder\Comparators;

use InvalidArgumentException;
use function in_array;

/**
 * Class Comparator
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Comparator/Comparator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Filer\Comparators
 */
class Comparator
{

    /**
     * The target value (i.e. to be tested/compared against)
     *
     * @var string
     */
    private string $target;

    /**
     * The comparator operator that determines the nature of the test comparison (semi-strict equals, '==', by default)
     *
     * @var string
     */
    private string $operator = '==';

    /**
     * Gets the target value.
     *
     * @return string The target value
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target)
    {
        $this->target = $target;
    }

    /**
     * Gets the comparison operator.
     *
     * @return string The operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Sets the comparison operator.
     *
     * @param string $operator
     * @throws InvalidArgumentException
     */
    public function setOperator(string $operator)
    {
        if ('' === $operator) {
            $operator = '==';
        }

        if (!in_array($operator, ['>', '<', '>=', '<=', '==', '!='])) {
            throw new InvalidArgumentException('The supplied operator, {{' . $operator . '}}, is  not ' .
                'recognized as a valid comparator.');
        }

        $this->operator = $operator;
    }

    /**
     * Tests against the target.
     *
     * @param mixed $test A test value
     * @return bool
     */
    public function test(mixed $test)
    {
        switch ($this->operator) {
            case '>':
                return $test > $this->target;
            case '>=':
                return $test >= $this->target;
            case '<':
                return $test < $this->target;
            case '<=':
                return $test <= $this->target;
            case '!=':
                return $test != $this->target;
        }

        return $test == $this->target;
    }

}