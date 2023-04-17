<?php

namespace nomit\Drive\Finder\Iterators\Filters\Patterns;

/**
 * Class FileContentFilter
 *
 * Filters files by their contents using set pattern(s) (either RegExps or strings).
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Iterator/FilecontentFilterIterator.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Włodzimierz Gajda <gajdaw@gajdaw.pl>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class FileContentFilter extends PatternFilter
{

    /**
     * Filters the subject {@link Iterator} items.
     *
     * @return bool True if the item should be kept, false otherwise
     */
    public function accept(): bool
    {
        if (!$this->match_regexs && !$this->no_match_regexs) {
            return true;
        }

        $file_info = $this->current();

        if ($file_info->isDir() || !$file_info->isReadable()) {
            return false;
        }

        $content = $file_info->getContents();

        if (!$content) {
            return false;
        }

        return $this->isAccepted($content);
    }

    /**
     * Converts a string to regular expression, if necessary.
     *
     * @param string $string
     * @return string
     */
    protected function toRegex(string $string): string
    {
        return $this->isRegex($string) ? $string : '/' . preg_quote($string, '/') . '/';
    }

}