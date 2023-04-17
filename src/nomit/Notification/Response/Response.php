<?php

namespace nomit\Notification\Response;

use nomit\Notification\Notification\NotificationInterface;

final class Response implements ResponseInterface
{

    private array $options = [];

    public function __construct(
        private array $notifications,
        private array $context
    )
    {
    }

    public function count()
    {
        return count($this->notifications);
    }

    /**
     * @return array
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setOption(string $name, mixed $value): ResponseInterface
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    public function getOption(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function toArray(bool $filter = false): array
    {
        $notifications = array_map(function(NotificationInterface $notification) {
            return $notification->toArray();
        }, $this->getNotifications());

        $response = [
            'notifications' => $notifications,
            'context' => $this->getContext(),
            'options' => $this->getOptions()
        ];

        if(false === $filter) {
            return $response;
        }

        return array_filter($response);
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}