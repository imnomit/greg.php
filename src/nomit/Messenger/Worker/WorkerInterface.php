<?php

namespace nomit\Messenger\Worker;

use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Work\CallbackInterface;

interface WorkerInterface
{

    public function build(EnvelopeInterface $envelope, array $handlers = []): CallbackInterface;

    public function run(CallbackInterface $process, callable $callback = null): void;

    public function catch(): self;

}