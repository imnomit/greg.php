<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\RateLimiter\Exception;

use RuntimeException;
use nomit\RateLimiter\RateLimit;
use Throwable;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 *
 * @experimental in 5.3
 */
class MaxWaitDurationExceededException extends \nomit\Exception\RuntimeException implements ExceptionInterface
{
    private $rateLimit;

    public function __construct(string $message, RateLimit $rateLimit, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->rateLimit = $rateLimit;
    }

    public function getRateLimit(): RateLimit
    {
        return $this->rateLimit;
    }
}
