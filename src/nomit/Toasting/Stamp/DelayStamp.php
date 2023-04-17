<?php

namespace nomit\Toasting\Stamp;

final class DelayStamp extends AbstractStamp
{

    public function __construct(
        private int $delay
    )
    {
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    public function toArray(): array
    {
        return [
            'delay' => $this->getDelay()
        ];
    }

}