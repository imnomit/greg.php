<?php

namespace nomit\Messenger\Stamp;

use nomit\Messenger\Worker\WorkerInterface;

class WorkerStamp implements StampInterface
{

    protected WorkerInterface $worker;

    public function __construct(WorkerInterface $worker)
    {
        $this->worker = $worker;
    }

    public function getName(): string
    {
        return 'WorkerStamp';
    }

    /**
     * @return WorkerInterface
     */
    public function getWorker(): WorkerInterface
    {
        return $this->worker;
    }

}