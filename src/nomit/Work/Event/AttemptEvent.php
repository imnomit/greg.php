<?php

namespace nomit\Work\Event;

use nomit\Work\CallbackInterface;

class AttemptEvent extends WorkerEvent
{

    protected int $attempt_time;

    public function __construct(CallbackInterface $process, int $attemptTime)
    {
        parent::__construct($process);

        $this->attempt_time = $attemptTime;
    }

    /**
     * @return int
     */
    public function getAttemptTime(): int
    {
        return $this->attempt_time;
    }

}