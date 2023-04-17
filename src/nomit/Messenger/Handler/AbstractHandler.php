<?php

namespace nomit\Messenger\Handler;

use nomit\Messenger\Exception\BadMethodCallException;
use nomit\Messenger\Message\MessageInterface;
use nomit\Messenger\Worker\WorkerInterface;

abstract class AbstractHandler implements HandlerInterface
{

    protected mixed $job;

    protected ?\Throwable $exception = null;

    public function getClass(): string
    {
        return get_class($this);
    }

    public function toString(): string
    {
        return $this->getClass();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}