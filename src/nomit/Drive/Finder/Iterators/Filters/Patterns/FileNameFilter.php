<?php

namespace nomit\Drive\Finder\Iterators\Filters\Patterns;

use nomit\Drive\Finder\Glob;

/**
 * Class FileNameFilter
 *
 * Filters files by patterns (a regular expression, a glob, or a string).
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/FilenameFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Włodzimierz Gajda <gajdaw@gajdaw.pl>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class FileNameFilter extends PatternFilter
{

    /**
     * Filters the subject {@link Iterator} items.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        return $this->isAccepted($this->current()->getFilename());
    }

    /**
     * Converts glob to regexp.
     *
     * PCRE patterns are left unchanged.
     *
     * Glob strings are transformed with {@see Glob::toRegex()}.
     *
     * @param string $string Pattern: glob or regexp
     * @return string A regular expression corresponding to a given glob or regular expression
     */
    protected function toRegex(string $string): string
    {
        return $this->isRegex($string) ? $string : Glob::toRegex($string);
    }

}