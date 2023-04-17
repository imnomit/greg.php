<?php

namespace nomit\Security\Authentication\Firewall\Listener;

use nomit\Kernel\Event\RequestEvent;

abstract class AbstractFirewallListener implements FirewallListenerInterface
{

    public static function getPriority(): int
    {
        return 0;
    }

    final public function __invoke(RequestEvent $event): void
    {
        if(false !== $this->supports($event->getRequest())) {
            $this->authenticate($event);
        }
    }

}