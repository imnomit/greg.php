<?php

namespace nomit\Messenger\Envelope;

use nomit\Messenger\Message\MessageInterface;
use nomit\Messenger\Stamp\StampInterface;

class Envelope implements EnvelopeInterface
{

    protected array $stamps = [];

    protected MessageInterface $message;

    protected string $class;

    protected int $timestamp;

    public static function wrap(MessageInterface $message, array $stamps = []): EnvelopeInterface
    {
        $envelope = $message instanceof self ? $message : new self($message);

        return $envelope->with(...$stamps);
    }

    public function __construct(MessageInterface $message, array $stamps = [])
    {
        $this->message = $message;
        $this->class = get_class($message);
        $this->timestamp = time();

        foreach($stamps as $stamp) {
            $this->stamp($stamp);
        }
    }

    public function getName(): string
    {
        return $this->message->getName();
    }

    public function stamp(StampInterface $stamp): EnvelopeInterface
    {
        $this->stamps[get_class($stamp)][] = $stamp;

        return $this;
    }

    public function remove(string $stamp): self
    {
        unset($this->stamps[$this->resolve($stamp)]);

        return $this;
    }

    public function with(StampInterface ...$stamps): EnvelopeInterface
    {
        $clone = clone $this;

        foreach($stamps as $stamp) {
            $clone->stamp($stamp);
        }

        return $clone;
    }

    public function without(string $stamp): EnvelopeInterface
    {
        $clone = clone $this;

        $clone->remove($stamp);

        return $clone;
    }

    public function withoutType(string $type): EnvelopeInterface
    {
        $clone = clone $this;
        $type = $this->resolve($type);

        foreach($clone->stamps as $class => $stamps) {
            if($class === $type || is_subclass_of($class, $type)) {
                unset($clone->stamps[$class]);
            }
        }

        return $clone;
    }

    public function last(string $stamp): ?StampInterface
    {
        return isset($this->stamps[$name = $this->resolve($stamp)]) ? end($this->stamps[$name]) : null;
    }

    public function all(string $stamp = null): array
    {
        if(null !== $stamp) {
            return $this->stamps[$this->resolve($stamp)];
        }

        return $this->stamps;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    protected function resolve(string $name): string
    {
        return class_exists($name) ? (new \ReflectionClass($name))->getName() : $name;
    }

}