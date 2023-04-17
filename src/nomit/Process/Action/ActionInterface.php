<?php

namespace nomit\Process\Action;

use nomit\Closure\SerializableClosure;
use nomit\Console\Command\CommandInterface;
use nomit\Process\Context\ContextInterface;
use nomit\Process\Controller\ControllerInterface;
use nomit\Process\ProcessInterface;
use nomit\Console\Request\RequestInterface;
use nomit\Console\Response\ResponseInterface;
use nomit\Utility\Concern\Serializable;

interface ActionInterface
{

    public const EVENT_CONSTRUCT = 128;
    public const EVENT_FORK = 256;
    public const EVENT_START = 2;
    public const EVENT_SUCCESS = 4;
    public const EVENT_ERROR = 8;
    public const EVENT_FAILURE = 16;
    public const EVENT_TIMEOUT = 32;
    public const EVENT_TERMINATE = 64;

    public function bind(int|string $event, callable|SerializableClosure $callback): self;

    public function trigger(int|string $event, ControllerInterface $controller, ContextInterface $context): mixed;

    public function run(ControllerInterface $controller, ContextInterface $context, ...$arguments): bool;

    public function setException(\Throwable $exception): self;

    public function hasException(): bool;

    public function getException(): ?\Throwable;

    public function getCallbacks(): array;

}