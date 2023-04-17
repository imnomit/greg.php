<?php

namespace nomit\Process\Context;

use nomit\Console\Command\CommandInterface;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;

/**
 * Class Context
 * @package nomit\Process\Context
 */
class Context implements ContextInterface
{

    protected array $context = [
        'commands' => []
    ];

    public function __construct(array $context = [])
    {
        foreach($context as $name => $value) {
            $this->set($name, $value);
        }
    }

    public function __set($property, $value)
    {
        $this->set($property, $value);

        return $this;
    }

    public function __isset($property): bool
    {
        return isset($this->context[$property]);
    }

    public function __get($property): mixed
    {
        return $this->get($property);
    }

    public function set(string $name, mixed $value): ContextInterface
    {
        $this->context[$name] = $value;

        return $this;
    }

    public function get(string $name): mixed
    {
        return $this->context[$name] ?? null;
    }

    public function normalize(mixed $value): mixed
    {
        if ($value instanceof \Throwable) {
            $value = [
                'class' => get_class($value),
                'message' => $value->getMessage(),
                'code' => $value->getCode(),
                'file' => $value->getFile(),
                'line' => $value->getLine(),
            ];
        }

        return $value;
    }

    public function getCommands(): array
    {
        return $this->context['commands'];
    }

    public function setCommand(string $name, array $parameters): self
    {
        $this->getCommands()[$name] = $parameters;

        return $this;
    }

    public function getCommand(string $command): ?CommandInterface
    {
        return $this->getCommands()[$command]['command'] ?? null;
    }

    public function getInput(string $command): ?InputInterface
    {
        return $this->getCommands()[$command]['request'] ?? null;
    }

    public function getOutput(string $command): ?OutputInterface
    {
        return $this->getCommands()[$command]['response'] ?? null;
    }

    public function getArguments(string $command): ?array
    {
        return $this->getCommands()[$command]['arguments'] ?? null;
    }

    public function getOptions(string $command): ?array
    {
        return $this->getCommands()[$command]['options'] ?? null;
    }

    public function toArray(): array
    {
        $context = [];

        foreach($this->context as $name => $value) {
            $context[$name] = $this->normalize($value);
        }

        return $context;
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}