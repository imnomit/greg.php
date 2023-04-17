<?php

namespace nomit\Security\Authentication\Session;

use nomit\Dumper\Dumper;
use nomit\Exception\InvalidArgumentException;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Security\Security;
use nomit\Web\Request\RequestInterface;

class DefaultSessionAuthenticationStrategy implements SessionAuthenticationStrategyInterface
{

    private string $strategy;

    public function __construct(string $strategy)
    {
        if(!in_array($strategy, self::STRATEGIES)) {
            throw new InvalidArgumentException(sprintf('The supplied session strategy, "%s", is invalid. The available session strategies are: %s.', $strategy, implode(', ', self::STRATEGIES)));
        }

        $this->strategy = $strategy;
    }

    public function onAuthentication(RequestInterface $request, TokenInterface $token): void
    {
        $request->getSession()->set(Security::AUTHENTICATION_TOKEN_SESSION_ATTRIBUTE, $token);
    }

    public function onLogout(RequestInterface $request): void
    {
        $request->getSession()->remove(Security::AUTHENTICATION_TOKEN_SESSION_ATTRIBUTE);

        switch($this->strategy) {
            case self::NONE:
                return;

            case self::MIGRATE:
                $request->getSession()->regenerate($request);

                return;

            case self::INVALIDATE:
                $request->getSession()->invalidate();

                return;

            default:
                throw new InvalidArgumentException(sprintf('The supplied session strategy, "%s", is invalid. The available session strategies are: %s.', $this->strategy, implode(', ', self::STRATEGIES)));
        }
    }

}