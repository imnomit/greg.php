<?php

namespace nomit\Calendar\Event\Bag;

use nomit\Calendar\Event\EventInterface;
use nomit\Calendar\Period\PeriodInterface;

class EventBag implements EventBagInterface
{

    public function __construct(
        private array $events = []
    )
    {
    }

    public function add(EventInterface $event): EventBagInterface
    {
        $this->events[] = $event;

        return $this;
    }

    public function has(mixed $index): bool
    {
        return count($this->get($index)) > 0;
    }

    public function get(mixed $index): array
    {
        return array_filter($this->events, static function(EventInterface $event) use($index) {
            return ($index instanceof PeriodInterface && $index->containsEvent($event))
                || ($index instanceof \DateTimeInterface && $event->contains($index));
        });
    }

    public function remove(EventInterface $event): void
    {
        foreach($this->events as $index => $subjectEvent) {
            if($event->getId() === $subjectEvent->getId()) {
                unset($this->events[$index]);
            }
        }
    }

    public function all(): array
    {
        return $this->events;
    }

    public function clear(): void
    {
        $this->events = [];
    }

    public function count(): int
    {
        return count($this->events);
    }

    public function toArray(): array
    {
        return $this->all();
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}