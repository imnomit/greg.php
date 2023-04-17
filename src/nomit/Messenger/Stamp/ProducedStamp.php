<?php

namespace nomit\Messenger\Stamp;

class ProducedStamp implements StampInterface
{

    protected int $timestamp;

    public function __construct(int $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getName(): string
    {
        return 'ProducedStamp';
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

}