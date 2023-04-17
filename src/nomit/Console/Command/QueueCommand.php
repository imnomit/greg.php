<?php

namespace nomit\Console\Command;

use nomit\Console\Exception\ConsoleException;
use nomit\Console\Exception\QueueException;
use nomit\Console\Queue\QueueInterface;

abstract class QueueCommand extends Command
{

    protected array $queues;

    public function hasQueue(string $queueName): bool
    {
        foreach($this->queues as $queue) {
            if($queue->getName() === $queueName) {
                return true;
            }
        }

        return false;
    }

    public function getQueue(string $queueName): QueueInterface
    {
        foreach($this->queues as $queue) {
            if($queue->getName() === $queueName) {
                return $queue;
            }
        }

        throw new QueueException(sprintf('No console queue with the queue name "%s" exists.', $queueName));
    }

}