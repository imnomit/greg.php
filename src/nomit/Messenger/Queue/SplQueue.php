<?php

namespace nomit\Messenger\Queue;

use nomit\Messenger\Envelope\EnvelopeInterface;

class SplQueue extends AbstractQueue
{

    protected \SplQueue $queue;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->queue = new \SplQueue();
        $this->queue->setIteratorMode(\SplQueue::IT_MODE_DELETE);
    }

    public function count(): int
    {
        $this->requiresOpen();

        return $this->queue->count();
    }

    public function enqueue(EnvelopeInterface $envelope): void
    {
        $this->requiresOpen();

        $this->queue->enqueue($envelope);
    }

    public function dequeue(): ?EnvelopeInterface
    {
        $this->requiresOpen();

        if ($this->count()) {
            return $this->queue->dequeue();
        }

        usleep(10000);

        return null;
    }

    public function all(int $index = 0, int $limit = null): array
    {
        $this->requiresOpen();

        $envelopes = [];
        $queue = clone $this->queue;
        $key = 0;

        while ($queue->count()
            && ($limit !== null && count($envelopes) < $limit)
            && $envelope = $queue->dequeue()
        ) {
            if ($key++ < $index) {
                continue;
            }

            $envelopes[] = $envelope;
        }

        return $envelopes;
    }

}