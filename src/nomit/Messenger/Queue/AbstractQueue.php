<?php

namespace nomit\Messenger\Queue;

use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Exception\InvalidOperationException;

abstract class AbstractQueue implements QueueInterface
{

    protected bool $closed = false;

    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function close(): void
    {
        $this->closed = true;
    }

    public function acknowledge(EnvelopeInterface $envelope): void
    {
        $this->requiresOpen();
    }

    protected function requiresOpen(): void
    {
        if($this->closed) {
            throw new InvalidOperationException(sprintf('The requested operation cannot be performed, as the queue named "%s" has already been closed.', $this->name));
        }
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}