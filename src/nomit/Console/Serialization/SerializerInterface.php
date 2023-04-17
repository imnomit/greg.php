<?php

namespace nomit\Console\Serialization;

use nomit\Console\Command\CommandInterface;

interface SerializerInterface
{

    public function serialize(CommandInterface $command): string;

    public function unserialize(string $payload): ?CommandInterface;

}