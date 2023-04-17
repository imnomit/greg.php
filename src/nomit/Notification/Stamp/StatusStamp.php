<?php

namespace nomit\Notification\Stamp;

use nomit\Notification\Status;
use nomit\Notification\Exception\InvalidArgumentException;

final class StatusStamp extends AbstractStamp
{

    public function __construct(
        private int $status = Status::STATUS_UNREAD
    )
    {
    }

    public function getName(): string
    {
        return 'status';
    }

    public function setStatus(int $status): self
    {
        if(!in_array($status, Status::STATUSES)) {
            throw new InvalidArgumentException(sprintf('The supplied status, "%s", is not supported. The supported statuses are: "%s".', $status, implode(', ', Status::STATUSES)));
        }

        $this->status = $status;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isStatus(int $comparator): bool
    {
        return $this->status === $comparator;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'status' => $this->getStatus()
        ];
    }

}