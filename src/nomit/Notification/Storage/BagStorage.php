<?php

namespace nomit\Notification\Storage;

use nomit\Dumper\Dumper;
use nomit\Notification\Notification\NotificationInterface;
use nomit\Notification\Stamp\UuidStamp;
use nomit\Notification\Storage\Bag\ArrayBag;
use nomit\Notification\Storage\Bag\BagInterface;
use nomit\Security\User\UserInterface;

final class BagStorage implements StorageInterface
{

    private BagInterface $bag;

    public function __construct(BagInterface $bag = null)
    {
        $this->bag = $bag ?? new ArrayBag();
    }

    public function all(UserInterface $user): array
    {
        return UuidStamp::indexByUuid($this->bag->get($user));
    }

    public function add(array|NotificationInterface $notifications, UserInterface $user): bool
    {
        $notifications = \is_array($notifications) ? $notifications : [$notifications];
        $notifications = UuidStamp::indexByUuid($notifications);

        return $this->bag->set($notifications, $user, false, false);
    }

    public function update(array|NotificationInterface $notifications, UserInterface $user): bool
    {
        $notifications = \is_array($notifications) ? $notifications : [$notifications];
        $notifications = UuidStamp::indexByUuid($notifications);

        return $this->bag->set(array_merge($this->all($user), $notifications), $user, true, true);
    }

    public function remove(array|NotificationInterface $notifications, UserInterface $user): bool
    {
        $notifications = \is_array($notifications) ? $notifications : [$notifications];
        $notifications = UuidStamp::indexByUuid($notifications);

        return $this->bag->set(array_diff_key($this->all($user), $notifications), $user, true, false);
    }

    public function clear(UserInterface $user): bool
    {
        return $this->bag->set([], $user, true, false);
    }

    public function count(UserInterface $user): int
    {
        return $this->bag->count($user);
    }

    public function toArray(UserInterface $user): array
    {
        return $this->all($user);
    }

    public function __toArray(UserInterface $user): array
    {
        return $this->toArray($user);
    }

}