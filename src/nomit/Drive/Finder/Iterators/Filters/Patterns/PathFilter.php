<?php

namespace nomit\Drive\Finder\Iterators\Filters\Patterns;

use const DIRECTORY_SEPARATOR;

/**
 * Class PathFilter
 *
 * Filters files by path patterns (e.g. some/special/dir).
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/PathFilterIterator.php
 *
 * @author Fabien Potencier  <fabien@symfony.com>
 * @author Włodzimierz Gajda <gajdaw@gajdaw.pl>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class PathFilter extends PatternFilter
{

    /**
     * Filters the subject {@link Iterator} items.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        $filename = $this->current()->getRelativePathname();

        if ('\\' === DIRECTORY_SEPARATOR) {
            $filename = str_replace('\\', '/', $filename);
        }

        return $this->isAccepted($filename);
    }

    /**
     * Converts strings to regular expressions.
     *
     * PCRE patterns are left unchanged.
     *
     * Use only the slash (/) character as directory separator (on Windows also).
     *
     * Default conversion:
     * @param string $string Pattern: regular expression or directory name
     * @return string Regular expression corresponding to a given string or regular expression
     * @example 'lorem/ipsum/dolor' ==>  'lorem\/ipsum\/dolor/'
     *
     */
    protected function toRegex(string $string): string
    {
        return $this->isRegex($string) ? $string : '/' . preg_quote($string, '/') . '/';
    }

}