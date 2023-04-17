<?php

namespace nomit\Messenger\Queue;

use nomit\Utility\Concern\Stringable;
use nomit\Messenger\Envelope\EnvelopeInterface;

interface QueueInterface extends Stringable
{

    public function count(): int;

    public function enqueue(EnvelopeInterface $envelope): void;

    public function dequeue(): ?EnvelopeInterface;

    public function acknowledge(EnvelopeInterface $envelope): void;

    public function close(): void;

    public function all(int $index = 0, int $limit = null): array;

}