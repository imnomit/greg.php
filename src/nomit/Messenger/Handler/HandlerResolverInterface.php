<?php

namespace nomit\Messenger\Handler;

use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Handler\HandlerFactoryInterface;
use nomit\Messenger\Handler\HandlerInterface;

interface HandlerResolverInterface
{

    public function add(HandlerFactoryInterface $receiver): self;

    public function supports(mixed $receiver): bool;

    public function resolve(mixed $receiver, EnvelopeInterface $envelope): ?HandlerInterface;

}