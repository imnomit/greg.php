<?php

namespace nomit\FileSystem\Finder\Iterators\Filters\Patterns;

use FilterIterator;
use Iterator;

/**
 * Class PatternFilter
 *
 * Filters files using patterns (regular expressions, globs or strings).
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/MultiplePcreFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters\Abstracts
 */
abstract class PatternFilter extends FilterIterator
{

    /**
     * Regular-expressions that MUST be matched.
     *
     * @var array
     */
    protected array $match_regexs = [];

    /**
     * Regular-expressions that MUST NOT be matched.
     *
     * @var array
     */
    protected array $no_match_regexs = [];

    /**
     * @param Iterator $iterator The Iterator to filter
     * @param string[] $match_patterns An array of patterns MUST be matched
     * @param string[] $no_match_patterns An array of patterns that MUST NOT be matched
     */
    public function __construct(Iterator $iterator, array $match_patterns, array $no_match_patterns)
    {
        foreach ($match_patterns as $pattern) {
            $this->match_regexs[] = $this->toRegex($pattern);
        }

        foreach ($no_match_patterns as $pattern) {
            $this->no_match_regexs[] = $this->toRegex($pattern);
        }

        parent::__construct($iterator);
    }

    /**
     * Converts a supplied string into a regular expression.
     *
     * @param string $string
     * @return string
     */
    abstract protected function toRegex(string $string);

    /**
     * Checks whether the string is accepted by the subject regular-expression filters.
     *
     * If there no regular expressions have been defined in the class, this method will accept the string. Such
     *  cases can be handled by child classes before calling the method if they want to apply a different
     *  behavior.
     *
     * @return bool
     */
    protected function isAccepted(string $string): bool
    {
        // If the subject matches at least one "no-match" regular expression, exclude it.
        foreach ($this->no_match_regexs as $regex) {
            if (preg_match($regex, $string)) {
                return false;
            }
        }

        // If the subject matches at least one "match" regular expression, include/keep it.
        if ($this->match_regexs) {
            foreach ($this->match_regexs as $regex) {
                if (preg_match($regex, $string)) {
                    return true;
                }
            }

            return false;
        }

        // If no match rules have been defined, accept the subject by default
        return true;
    }

    /**
     * Checks whether a supplied string is a regular expression.
     *
     * @param string $string
     * @return bool
     */
    protected function isRegex(string $string): bool
    {
        if (preg_match('/^(.{3,}?)[imsxuADU]*$/', $string, $m)) {
            $start = substr($m[1], 0, 1);
            $end = substr($m[1], -1);

            if ($start === $end) {
                return !preg_match('/[*?[:alnum:] \\\\]/', $start);
            }

            foreach ([['{', '}'], ['(', ')'], ['[', ']'], ['<', '>']] as $delimiters) {
                if ($start === $delimiters[0] && $end === $delimiters[1]) {
                    return true;
                }
            }
        }

        return false;
    }

}