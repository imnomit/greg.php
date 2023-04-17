<?php

namespace nomit\Messenger\Stamp;

use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Handler\HandlerInterface;
use nomit\Messenger\Worker\WorkerInterface;

class HandledStamp extends WorkerStamp
{

    protected WorkerInterface $worker;

    protected EnvelopeInterface $envelope;

    protected HandlerInterface $handler;

    public function __construct(WorkerInterface $worker, EnvelopeInterface $envelope, HandlerInterface $handler)
    {
        parent::__construct($worker);

        $this->envelope = $envelope;
        $this->handler = $handler;
    }

    public function getName(): string
    {
        return 'HandledStamp';
    }

    /**
     * @return EnvelopeInterface
     */
    public function getEnvelope(): EnvelopeInterface
    {
        return $this->envelope;
    }

    /**
     * @return HandlerInterface
     */
    public function getHandler(): HandlerInterface
    {
        return $this->handler;
    }

    public function getEnvelopeName(): string
    {
        return $this->envelope->getName();
    }

    public function getEnvelopeClass(): string
    {
        return get_class($this->envelope);
    }

    public function getHandlerClass(): string
    {
        return get_class($this->handler);
    }

}