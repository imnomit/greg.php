<?php

namespace nomit\Messenger\Repository;

use nomit\Messenger\Envelope\EnvelopeInterface;

interface HandlerRepositoryInterface
{

    public function add(EnvelopeInterface $envelope, mixed $receivers): self;

    public function has(EnvelopeInterface $envelope): bool;

    public function get(EnvelopeInterface $envelope): array;

    public function remove(EnvelopeInterface $envelope = null): self;

    public function all(): \SplObjectStorage;

}