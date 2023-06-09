<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Lock\Exception;

use RuntimeException;

/**
 * LockAcquiringException is thrown when an issue happens during the acquisition of a lock.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class LockAcquiringException extends RuntimeException implements ExceptionInterface
{
}
