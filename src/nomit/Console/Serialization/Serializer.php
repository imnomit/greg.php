<?php

namespace nomit\Console\Serialization;

use nomit\Console\Command\CommandInterface;
use nomit\Serialization\SerializerResolverInterface;

final class Serializer implements SerializerInterface
{

    public function __construct(
        private SerializerResolverInterface $serializer,
        private string $format = 'json'
    )
    {
    }

    public function serialize(CommandInterface $command): string
    {
        return $this->serializer->serialize($command, $this->format);
    }

    public function unserialize(string $payload): ?CommandInterface
    {
        return $this->serializer->unserialize($payload, $this->format);
    }

}