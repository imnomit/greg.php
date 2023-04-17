<?php

namespace nomit\Kernel\EventListener;

use nomit\EventDispatcher\EventSubscriberInterface;
use nomit\Kernel\Event\ControllerEvent;
use nomit\Kernel\Event\KernelEvents;

final class ControllerEventListener implements EventSubscriberInterface
{

    public function __construct(
        private array $userProviders
    )
    {
    }

    public function onController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        $controller->setUserProviders($this->userProviders);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onController'
        ];
    }

    public static function getDispatcherName(): ?string
    {
        return null;
    }

}