<?php

namespace nomit\Process\Action;

use nomit\Work\Callback;
use nomit\Work\CallbackInterface;
use nomit\Work\Job\JobInterface;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Process\Context\ContextInterface;
use nomit\Process\Controller\ControllerInterface;

class CallbackAction extends AbstractAction
{

    protected CallbackInterface $callback;

    public function __construct(CallbackInterface|JobInterface $callback, EventDispatcherInterface $dispatcher = null)
    {
        $this->setCallback($callback, $dispatcher);
    }

    public function setCallback(CallbackInterface|JobInterface $callback, EventDispatcherInterface $dispatcher = null): self
    {
        if($callback instanceof JobInterface) {
            $callback = new Callback($callback, $dispatcher);
        }

        $this->callback = $callback;

        return $this;
    }

    public function hasCallback(): bool
    {
        return $this->callback instanceof CallbackInterface;
    }

    public function getCallback(): CallbackInterface
    {
        return $this->callback;
    }

    public function run(ControllerInterface $controller, ContextInterface $context, ...$arguments): bool
    {
        if(!$this->hasCallback()) {
            return false;
        }

        foreach([$controller, $context, ...$arguments] as $argument) {
            $this->callback->pushArgument($argument);
        }

        try {
            $this->callback->run();
        } catch(\Throwable $exception) {
            return false;
        }

        return true;
    }

}