<?php

namespace nomit\Notification;

use nomit\Notification\Exception\RuntimeException;
use nomit\Notification\Message\Message;
use nomit\Notification\Message\MessageInterface;
use nomit\Notification\Message\ParameterMessage;
use nomit\Notification\Notification\Notification;
use nomit\Notification\Notification\NotificationInterface;
use nomit\Notification\Response\ResponderInterface;
use nomit\Notification\Response\ResponseInterface;
use nomit\Notification\Storage\StorageManagerInterface;
use nomit\Security\User\UserInterface;

final class Notifier implements NotifierInterface
{

    private string $defaultView;

    public function __construct(
        ?string $defaultView,
        private ResponderInterface $responder,
        private StorageManagerInterface $storageManager,
    )
    {
        $this->defaultView = $defaultView ?: 'json';
    }

    public function produce(UserInterface $user, string $title, string $message, array $stamps = [], array $parameters = []): NotificationInterface
    {
        if(empty($parameters)) {
            $message = new Message($title, $message);
        } else {
            $message = new ParameterMessage($title, $message, $parameters);
        }

        $notification = Notification::wrap($message, $stamps);

        if(!$this->add($user, $notification)) {
            throw new RuntimeException(sprintf('An error occurred while attempting to push the supplied notification, message "%s", to the user with the user ID "%s".', $notification->getMessage()->toString(), $user->getUserId()));
        }

        return $notification;
    }

    public function add(UserInterface $user, array|MessageInterface|NotificationInterface $notification): bool
    {
        if($notification instanceof MessageInterface) {
            $notification = Notification::wrap($notification);
        }

        return $this->storageManager->add($notification, $user);
    }

    public function load(UserInterface $user, array $criteria = [], array $context = []): ?ResponseInterface
    {
        return $this->responder->load($user, $criteria, $context);
    }

    public function render(UserInterface $user, array $criteria = [], ?string $view = 'html', array $context = []): mixed
    {
        return $this->responder->render($user, $criteria, $view ?? $this->defaultView, $context);
    }

    public function remove(array|NotificationInterface $notifications, UserInterface $user): bool
    {
        return $this->storageManager->remove($notifications, $user);
    }

    public function clear(UserInterface $user): bool
    {
        return $this->storageManager->clear($user);
    }

}