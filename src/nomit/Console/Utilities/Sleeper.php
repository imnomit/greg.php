<?php

namespace nomit\Console\Utilities;

class Sleeper implements SleeperInterface
{

    protected int|float $speed = 50000;

    public function speed(float|int $percentage): float
    {
        if(is_numeric($percentage) && $percentage > 0) {
            $this->speed *= (100 / $percentage);
        }

        return $this->speed;
    }

    public function sleep(int $seconds = null): void
    {
        usleep($seconds ?? $this->speed);
    }

}