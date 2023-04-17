<?php

namespace nomit\Drive\Finder\Comparators;

use InvalidArgumentException;

/**
 * Class NumberComparator
 *
 * Compiles a simple comparison to an anonymous subroutine, which can be called with a value to be tested again.
 *
 * This would be very pointless, if {@link NumberComparator} didn't understand  magnitudes.
 *
 * The target value may use magnitudes of kilobytes (k, ki), megabytes (m, mi), or gigabytes (g, gi). Those suffixed
 *  with an i use the appropriate 2**n version in accordance with the IEC standard:
 * @see http://physics.nist.gov/cuu/Units/binary.html
 *
 * Based on the Perl "Number::Compare" module, and derived from the Symfony package.
 *
 * For the original code from which this is derived:
 * @see https://github.com/symfony/finder/blob/5.x/Comparator/NumberComparator.php
 *
 * @author Fabien Potencier <fabien@symfony.com> PHP port
 * @author Richard Clamp <richardc@unixbeard.net> Perl version
 * @copyright 2004-2005 Fabien Potencier <fabien@symfony.com>
 * @copyright 2002 Richard Clamp <richardc@unixbeard.net>
 * @license MIT License <https://github.com/symfony/finder/blob/5.x/LICENSE>
 *
 * @package nomit\HarDriver\Filer\Comparators
 */
class NumberComparator extends Comparator
{

    /**
     * @param string|int $test A comparison string or an integer
     * @throws InvalidArgumentException If the supplied test value is not understood
     */
    public function __construct(string|int $test)
    {
        if (!preg_match('#^\s*(==|!=|[<>]=?)?\s*([0-9\.]+)\s*([kmg]i?)?\s*$#i', $test, $matches)) {
            throw new InvalidArgumentException('The supplied test value, {{' . $test . '}}, could not be ' .
                'understood as a number test, and is thus invalid.');
        }

        $target = $matches[2];

        if (!is_numeric($target)) {
            throw new InvalidArgumentException('The supplied test value, {{' . $test . '}} is not a valid ' .
                'number.');
        }

        if (isset($matches[3])) {
            // Switch through the extracted magnitude
            $target = match (strtolower($matches[3])) {
                'k' => 1000,
                'ki' => 1024,
                'm' => 1000000,
                'mi' => 1024 * 1024,
                'g' => 1000000000,
                'gi' => 1024 * 1024 * 1024,
            };
        }

        $this->setTarget($target);
        $this->setOperator($matches[1] ?? '==');
    }
}