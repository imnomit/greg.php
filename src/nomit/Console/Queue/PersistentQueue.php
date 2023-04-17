<?php

namespace nomit\Console\Queue;

use nomit\Console\Command\CommandInterface;
use nomit\Console\Exception\RuntimeException;
use nomit\Console\Serialization\SerializerInterface;
use nomit\Console\Queue\Persistence\PersistenceInterface;

final class PersistentQueue extends AbstractQueue
{

    private \SplObjectStorage $receipts;

    public function __construct(
        string $queueName,
        private PersistenceInterface $persistence,
        private SerializerInterface $serializer
    )
    {
        parent::__construct($queueName);

        $this->receipts = new \SplObjectStorage();

        $this->open();
    }

    public function open(): void
    {
        parent::open();

        $this->persistence->createQueue($this->getQueueName());
    }

    public function close(): void
    {
        parent::close();

        $this->persistence->removeQueue($this->getQueueName());
    }

    public function enqueue(CommandInterface $command): void
    {
        $this->cannotBeClosed();

        if(!$this->persistence->push($this->getQueueName(), $command->getName(), $this->serializer->serialize($command))) {
            throw new RuntimeException(sprintf('An error occurred while attempting to push to the configured persistence service, "%s", the supplied command, named "%s".', get_class($this->persistence), $command->getName()));
        }
    }

    public function acknowledge(CommandInterface $command): void
    {
        $this->cannotBeClosed();

        if($this->receipts->contains($command)) {
            $this->persistence->acknowledge($this->getQueueName(), $this->receipts[$command]);

            $this->receipts->detach($command);
        }
    }

    public function dequeue(int $duration = 5): ?CommandInterface
    {
        [$serialized, $receipt] = $this->persistence->pop($this->getQueueName(), $duration);

        if($serialized) {
            $command = $this->serializer->unserialize($serialized);

            if(!$command) {
                return null;
            }

            $this->receipts->attach($command, $receipt);

            return $command;
        }

        return null;
    }

}