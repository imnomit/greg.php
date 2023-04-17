<?php

namespace nomit\Toasting\EventListener;

use nomit\EventDispatcher\EventSubscriberInterface;
use nomit\Toasting\Event\FilterEnvelopesEvent;
use nomit\Toasting\Storage\StorageManagerInterface;

final class FilterEnvelopesEventListener implements EventSubscriberInterface
{

    public function __construct(
        private StorageManagerInterface $storageManager
    )
    {
    }

    public function onFilterEnvelopes(FilterEnvelopesEvent $event): void
    {
        foreach($event->getEnvelopes() as $envelope) {
            $envelope->read();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FilterEnvelopesEvent::class => 'onFilterEnvelopes'
        ];
    }

    public static function getDispatcherName(): ?string
    {
        return null;
    }

}