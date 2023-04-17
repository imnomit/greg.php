<?php

namespace nomit\Messenger\Result;

use nomit\Utility\Concern\Stringable;
use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Exception\InvalidArgumentException;
use nomit\Messenger\Message\MessageInterface;
use nomit\Utility\Bag\Bag;

class ResultsBag extends Bag implements ResultsBagInterface
{

    protected EnvelopeInterface $envelope;

    public function __construct(EnvelopeInterface $envelope, array $results = [])
    {
        $this->envelope = $envelope;

        parent::__construct($results);
    }

    public function getEnvelope(): EnvelopeInterface
    {
        return $this->envelope;
    }

    public function getResults(): mixed
    {
        $results = $this->all();

        if(count($results) === 1) {
            return $results[0];
        }

        return $results;
    }

}