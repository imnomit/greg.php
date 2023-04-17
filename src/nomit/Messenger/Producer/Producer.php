<?php

namespace nomit\Messenger\Producer;

use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Event\EnvelopeEvent;
use nomit\Messenger\Event\MessengerEvents;
use nomit\Messenger\Queue\QueueFactoryInterface;
use nomit\Messenger\Queue\QueueInterface;
use nomit\Messenger\Router\RouterInterface;
use nomit\Messenger\Stamp\QueuedStamp;
use nomit\Messenger\Stamp\ProducedStamp;
use nomit\Messenger\Utilities\MessengerUtilities;

class Producer implements ProducerInterface
{

    protected QueueFactoryInterface $queues;

    protected EventDispatcherInterface $dispatcher;

    public function __construct(QueueFactoryInterface $queues, EventDispatcherInterface $dispatcher)
    {
        $this->queues = $queues;
        $this->dispatcher = $dispatcher;
    }

    public function produce(EnvelopeInterface $envelope, string $queueName = null): EnvelopeInterface
    {
        if($envelope->all(ProducedStamp::class)) {
            return $envelope;
        }

        $queueName = $queueName ?? MessengerUtilities::guessQueue($envelope->getMessage());
        $queue = $this->queues->create($queueName);

        $envelope->stamp(new ProducedStamp(time()));

        $this->dispatcher->dispatch(new EnvelopeEvent($envelope, $queue), MessengerEvents::EVENT_PRODUCE);

        if($envelope->all(QueuedStamp::class)) {
            return $envelope;
        }

        $queue->enqueue($envelope);

        $envelope->stamp(new QueuedStamp($queueName));

        $this->dispatcher->dispatch(new EnvelopeEvent($envelope, $queue), MessengerEvents::EVENT_QUEUE);

        return $envelope;
    }

}