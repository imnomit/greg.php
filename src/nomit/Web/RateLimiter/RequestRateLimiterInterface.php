<?php

namespace nomit\Web\RateLimiter;

use nomit\RateLimiter\RateLimit;
use nomit\Web\Request\RequestInterface;

interface RequestRateLimiterInterface
{

    public function consume(RequestInterface $request): RateLimit;

    public function reset(RequestInterface $request): void;

}