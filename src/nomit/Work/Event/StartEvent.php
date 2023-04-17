<?php

namespace nomit\Work\Event;

use nomit\Work\CallbackInterface;

class StartEvent extends WorkerEvent
{

    protected int $start_time;

    public function __construct(CallbackInterface $process, int $startTime)
    {
        parent::__construct($process);

        $this->start_time = $startTime;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->start_time;
    }

}