<?php

namespace nomit\Notification\Storage;

use nomit\Dumper\Dumper;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Notification\Event\FilterNotificationsEvent;
use nomit\Notification\Event\RemovedNotificationsEvent;
use nomit\Notification\Event\RemoveNotificationsEvent;
use nomit\Notification\Event\AddedNotificationsEvent;
use nomit\Notification\Event\AddNotificationsEvent;
use nomit\Notification\Event\UpdatedNotificationsEvent;
use nomit\Notification\Event\UpdateNotificationsEvent;
use nomit\Notification\Notification\NotificationInterface;
use nomit\Security\User\UserInterface;
use Psr\Log\LoggerInterface;

final class StorageManager implements StorageManagerInterface
{

    private StorageInterface $storage;

    public function __construct(
        StorageInterface $storage = null,
        private ?EventDispatcherInterface $dispatcher = null,
        private ?LoggerInterface $logger = null,
        private array $criteria = []
    )
    {
        $this->storage = $storage ?: new BagStorage();
    }

    public function count(UserInterface $user): int
    {
        return $this->storage->count($user);
    }

    public function all(UserInterface $user): array
    {
        return $this->storage->all($user);
    }

    public function filter(UserInterface $user, array $criteria = []): array
    {
        $criteria = array_merge($this->criteria, $criteria);

        $criteria['delay'] = 0;

        $notifications = $this->all($user);

        $filterEvent = new FilterNotificationsEvent($notifications, $criteria, $user, $this->logger);

        $this->dispatcher?->dispatch($filterEvent);

        $notificationDifference = array_diff_key($notifications, $filterEvent->getNotifications());

        $this->logger?->debug('{count} notifications have been removed from the final notification array as part of the filtering process.', [
            'count' => count($notificationDifference),
            'notifications' => array_map(function(NotificationInterface $notification) {
                return $notification->toArray();
            }, $notificationDifference)
        ]);

        return $filterEvent->getNotifications();
    }

    public function add(array|NotificationInterface $notifications, UserInterface $user): bool
    {
        $notifications = \is_array($notifications) ? $notifications : [$notifications];

        $event = new AddNotificationsEvent($notifications, $user);

        $this->dispatcher?->dispatch($event);

        $eventNotifications = $event->getNotifications();

        if($this->storage->add($eventNotifications, $user)) {
            $event = new AddedNotificationsEvent($eventNotifications, $user);

            $this->dispatcher?->dispatch($event);

            return true;
        }

        return false;
    }

    public function update(array|NotificationInterface $notifications, UserInterface $user): bool
    {
        $notifications = \is_array($notifications) ? $notifications : [$notifications];

        $event = new UpdateNotificationsEvent($notifications, $user);

        $this->dispatcher?->dispatch($event);

        $eventNotifications = $event->getNotifications();

        if($this->storage->update($eventNotifications, $user)) {
            $event = new UpdatedNotificationsEvent($eventNotifications, $user);

            $this->dispatcher?->dispatch($event);

            return true;
        }

        return false;
    }

    public function remove(array|NotificationInterface $notifications, UserInterface $user): bool
    {
        $notifications = \is_array($notifications) ? $notifications : [$notifications];

        $event = new RemoveNotificationsEvent($notifications, $user);

        $event->setNotificationsToRemove($notifications);

        $this->dispatcher?->dispatch($event);

        $toKeep = $event->getNotificationsToKeep();
        $toRemove = $event->getNotificationsToRemove();

        if ($this->storage->update($toKeep, $user) && $this->storage->remove($toRemove, $user)) {
            $event = new RemovedNotificationsEvent($user, $toRemove, $toKeep);

            $this->dispatcher?->dispatch($event);

            return true;
        }

        return false;
    }

    public function clear(UserInterface $user): bool
    {
        return $this->storage->clear($user);
    }

}