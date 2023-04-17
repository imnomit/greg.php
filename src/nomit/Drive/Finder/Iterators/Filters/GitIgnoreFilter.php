<?php

namespace nomit\Drive\Finder\Iterators\Filters;

use function array_slice;
use function strlen;

/**
 * Class GitIgnoreFilter
 *
 * Matches against text to generate a regular expression that is equivalent to a "gitignore" pattern.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Gitignore.php
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Ahmed Abdou <mail@ahmd.io>
 * @copyright (C) 2004-2020 Fabien Potencier
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators\Filters
 */
class GitIgnoreFilter
{

    /**
     * Returns a regexp which is the equivalent of the gitignore pattern.
     *
     * @param string $gitignore_file_content
     * @return string The regexp
     */
    public static function toRegex(string $gitignore_file_content): string
    {
        $gitignore_file_content = preg_replace('/^[^\\\r\n]*#.*/m', '', $gitignore_file_content);
        $gitignore_lines = preg_split('/\r\n|\r|\n/', $gitignore_file_content);

        $positives = [];
        $negatives = [];

        foreach ($gitignore_lines as $i => $line) {
            $line = trim($line);

            if ('' === $line) {
                continue;
            }

            if (1 === preg_match('/^!/', $line)) {
                $positives[$i] = null;
                $negatives[$i] = self::getRegexFromGitignore(preg_replace('/^!(.*)/', '${3}', $line),
                    true);

                continue;
            }

            $negatives[$i] = null;
            $positives[$i] = self::getRegexFromGitignore($line);
        }

        $index = 0;
        $patterns = [];

        foreach ($positives as $pattern) {
            if (null === $pattern) {
                continue;
            }

            $negatives_after = array_filter(array_slice($negatives, ++$index));

            if ([] !== $negatives_after) {
                $pattern .= sprintf('(?<!%s)', implode('|', $negatives_after));
            }

            $patterns[] = $pattern;
        }

        return sprintf('/^((%s))$/', implode(')|(', $patterns));
    }

    private static function getRegexFromGitignore(string $gitignore_pattern, bool $negative = false): string
    {
        $regex = '';
        $is_relative_path = false;

        // If there is a separator at the beginning or middle (or both) of the pattern, then the pattern is relative
        // to the directory level of the particular ".gitignore" file itself
        $slash_position = strpos($gitignore_pattern, '/');

        if (false !== $slash_position && strlen($gitignore_pattern) - 1 !== $slash_position) {
            if (0 === $slash_position) {
                $gitignore_pattern = substr($gitignore_pattern, 1);
            }

            $is_relative_path = true;
            $regex .= '^';
        }

        if ('/' === $gitignore_pattern[strlen($gitignore_pattern) - 1]) {
            $gitignore_pattern = substr($gitignore_pattern, 0, -1);
        }

        $imax = strlen($gitignore_pattern);

        for ($i = 0; $i < $imax; ++$i) {
            $triple_characters = substr($gitignore_pattern, $i, 3);

            if ('**/' === $triple_characters || '/**' === $triple_characters) {
                $regex .= '.*';
                $i += 2;

                continue;
            }

            $double_characters = substr($gitignore_pattern, $i, 2);

            if ('**' === $double_characters) {
                $regex .= '.*';
                ++$i;

                continue;
            }

            if ('*/' === $double_characters) {
                $regex .= '[^\/]*\/?[^\/]*';
                ++$i;

                continue;
            }

            $c = $gitignore_pattern[$i];

            switch ($c) {
                case '*':
                    $regex .= $is_relative_path ? '[^\/]*' : '[^\/]*\/?[^\/]*';
                    break;

                case '/':
                case '.':
                case ':':
                case '(':
                case ')':
                case '{':
                case '}':
                    $regex .= '\\' . $c;

                    break;
                default:
                    $regex .= $c;
            }
        }

        if ($negative) {
            // A look-behind assertion has to be a fixed width (it can not have nested '|' statements)
            return sprintf('%s$|%s\/$', $regex, $regex);
        }

        return '(?>' . $regex . '($|\/.*))';
    }
}