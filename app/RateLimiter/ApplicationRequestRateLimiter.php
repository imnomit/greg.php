<?php

namespace Application\RateLimiter;

use nomit\RateLimiter\RateLimiterFactory;
use nomit\Web\RateLimiter\AbstractRequestRateLimiter;
use nomit\Web\Request\RequestInterface;

final class ApplicationRequestRateLimiter extends AbstractRequestRateLimiter
{

    public function __construct(
        private RateLimiterFactory $factory
    )
    {
    }

    protected function getLimiters(RequestInterface $request): array
    {
        return [
            $this->factory->create($request->getClientIp())
        ];
    }

}