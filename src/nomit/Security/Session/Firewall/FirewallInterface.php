<?php

namespace nomit\Security\Session\Firewall;

use nomit\EventDispatcher\EventSubscriberInterface;
use nomit\Kernel\Event\FinishRequestEvent;
use nomit\Kernel\Event\RequestEvent;

interface FirewallInterface extends EventSubscriberInterface
{

    public function onKernelRequest(RequestEvent $event): void;

    public function onKernelFinishRequest(FinishRequestEvent $event): void;

}