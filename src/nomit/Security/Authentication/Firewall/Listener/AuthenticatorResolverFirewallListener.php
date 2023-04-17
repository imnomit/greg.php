<?php

namespace nomit\Security\Authentication\Firewall\Listener;

use nomit\Kernel\Event\RequestEvent;
use nomit\Security\Authentication\Authenticator\AuthenticatorResolverInterface;
use nomit\Web\Request\RequestInterface;

class AuthenticatorResolverFirewallListener extends AbstractFirewallListener
{

    public function __construct(
        private AuthenticatorResolverInterface $resolver
    )
    {
    }

    public function supports(RequestInterface $request): ?bool
    {
        return $this->resolver->supports($request);
    }

    public function authenticate(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $response = $this->resolver->authenticate($request);

        if(null === $response) {
            return;
        }

        $event->setResponse($response);
    }

    public static function getPriority(): int
    {
        return -200;
    }

}