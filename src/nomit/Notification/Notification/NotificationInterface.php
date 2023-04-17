<?php

namespace nomit\Notification\Notification;

use nomit\Notification\Message\MessageInterface;
use nomit\Notification\Stamp\StampInterface;
use nomit\Utility\Concern\Arrayable;

interface NotificationInterface extends Arrayable
{

    public static function wrap(MessageInterface $message, array $stamps = []): self;

    public function stamp(StampInterface $stamp): self;

    public function remove(string $stamp): self;

    public function with(StampInterface ...$stamps): self;

    public function without(string $stamp): self;

    public function withoutType(string $type): self;

    public function has(string $stamp): bool;

    public function first(string $stamp): ?StampInterface;

    public function last(string $stamp): ?StampInterface;

    public function all(string $stamp = null): array;

    public function getMessage(): MessageInterface;

}