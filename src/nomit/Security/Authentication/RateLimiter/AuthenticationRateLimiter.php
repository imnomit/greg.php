<?php

namespace nomit\Security\Authentication\RateLimiter;

use nomit\RateLimiter\RateLimiterFactory;
use nomit\Security\Security;
use nomit\Web\RateLimiter\AbstractRequestRateLimiter;
use nomit\Web\Request\RequestInterface;

final class AuthenticationRateLimiter extends AbstractRequestRateLimiter
{

    public function __construct(
        private RateLimiterFactory $globalFactory,
        private RateLimiterFactory $localFactory
    )
    {
    }

    protected function getLimiters(RequestInterface $request): array
    {
        $username = $request->attributes->get(Security::LAST_USERNAME);
        $username = preg_match('//u', $username) ? mb_strtolower($username, 'UTF-8') : strtolower($username);

        return [
            $this->globalFactory->create($request->getClientIp()),
            $this->localFactory->create($username.'-'.$request->getClientIp()),
        ];
    }

}