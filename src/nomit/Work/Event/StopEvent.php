<?php

namespace nomit\Work\Event;

use nomit\Work\CallbackInterface;

class StopEvent extends WorkerEvent
{

    protected int $start_time;

    protected int $end_time;

    public function __construct(CallbackInterface $process, int $startTime, int $endTime)
    {
        parent::__construct($process);

        $this->start_time = $startTime;
        $this->end_time = $endTime;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->start_time;
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->end_time;
    }

}