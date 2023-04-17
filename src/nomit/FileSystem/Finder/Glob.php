<?php

namespace nomit\FileSystem\Finder;

use function strlen;

/**
 * Class GlobMatcher
 *
 * Matches globbing patterns against text.
 * <code>
 *     if(match_glob("foo.*", "foo.bar")) {
 *         echo "matched\n";
 *     }
 *
 *     // prints foo.bar and foo.baz
 *     $regex = glob_to_regex("foo.*");
 *
 *     for(['foo.bar', 'foo.baz', 'foo', 'bar'] as $t) {
 *         if(/$regex/) {
 *            echo "matched: $car\n";
 *         }
 *     }
 * </code>
 *
 * Glob implements glob(3) style matching that can be used to match against text, rather than fetching names
 *  from a filesystem.
 *
 * Based on the Perl "Text::Glob" module.
 *
 * Derived from the Symfony package.
 *
 * For the original code, from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Glob.php
 *
 * @author Fabien Potencier <fabien@symfony.com> PHP port
 * @author Richard Clamp <richardc@unixbeard.net> Perl version
 * @copyright 2004-2005 Fabien Potencier <fabien@symfony.com>
 * @copyright 2002 Richard Clamp <richardc@unixbeard.net>
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Finder\Iterators
 */
class Glob
{

    /**
     * Returns a regular expression which is the equivalent of the glob pattern.
     *
     * @param string $glob
     * @param bool $strict_leading_dot
     * @param bool $strict_wildcard_slash
     * @param string $delimiter
     * @return string
     */
    public static function toRegex(string $glob, bool $strict_leading_dot = true, bool $strict_wildcard_slash = true,
                                   string $delimiter = '#')
    {
        $first_byte = true;
        $escaping = false;
        $in_curlies = 0;
        $regex = '';
        $size_glob = strlen($glob);

        for ($i = 0; $i < $size_glob; ++$i) {
            $car = $glob[$i];

            if ($first_byte && $strict_leading_dot && '.' !== $car) {
                $regex .= '(?=[^\.])';
            }

            $first_byte = '/' === $car;

            if ($first_byte && $strict_wildcard_slash && isset($glob[$i + 2])
                && '**' === $glob[$i + 1] . $glob[$i + 2]
                && (!isset($glob[$i + 3]) || '/' === $glob[$i + 3])) {
                $car = '[^/]++/';

                if (!isset($glob[$i + 3])) {
                    $car .= '?';
                }

                if ($strict_leading_dot) {
                    $car = '(?=[^\.])' . $car;
                }

                $car = '/(?:' . $car . ')*';
                $i += 2 + isset($glob[$i + 3]);

                if ('/' === $delimiter) {
                    $car = str_replace('/', '\\/', $car);
                }
            }

            if ($delimiter === $car || '.' === $car || '(' === $car || ')' === $car || '|' === $car || '+' === $car
                || '^' === $car || '$' === $car) {
                $regex .= "\\$car";
            } else if ('*' === $car) {
                $regex .= $escaping ? '\\*' : ($strict_wildcard_slash ? '[^/]*' : '.*');
            } else if ('?' === $car) {
                $regex .= $escaping ? '\\?' : ($strict_wildcard_slash ? '[^/]' : '.');
            } else if ('{' === $car) {
                $regex .= $escaping ? '\\{' : '(';

                if (!$escaping) {
                    ++$in_curlies;
                }
            } else if ('}' === $car && $in_curlies) {
                $regex .= $escaping ? '}' : ')';

                if (!$escaping) {
                    --$in_curlies;
                }
            } else if (',' === $car && $in_curlies) {
                $regex .= $escaping ? ',' : '|';
            } else if ('\\' === $car) {
                if ($escaping) {
                    $regex .= '\\\\';
                    $escaping = false;
                } else {
                    $escaping = true;
                }

                continue;
            } else {
                $regex .= $car;
            }

            $escaping = false;
        }

        return $delimiter . '^' . $regex . '$' . $delimiter;
    }

}