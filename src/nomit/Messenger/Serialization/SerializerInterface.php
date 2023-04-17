<?php

namespace nomit\Messenger\Serialization;

use nomit\Messenger\Envelope\EnvelopeInterface;

interface SerializerInterface
{

    public function serialize(EnvelopeInterface $envelope, array $context = []): string;

    public function encode(EnvelopeInterface $envelope, array $context = []): array;

    public function unserialize(string $payload, array $context = []): ?EnvelopeInterface;

    public function decode(array $payload, array $context = []): ?EnvelopeInterface;

}