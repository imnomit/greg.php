<?php

namespace nomit\Stream;

use nomit\Utility\Concern\Stringable;

abstract class AbstractObservableStream extends Stream implements ObservableStreamInterface
{

    protected array $observers = [];

    public function addObserver(StreamObserverInterface $observer): ObservableStreamInterface
    {
        foreach($this->observers as $registeredObserver) {
            if($registeredObserver === $observer) {
                return $this;
            }
        }

        $this->observers[] = $observer;

        return $this;
    }

    public function removeObserver(StreamObserverInterface $observer): void
    {
        foreach($this->observers as $index => $registeredObserver) {
            if($registeredObserver === $observer) {
                unset($this->observers[$index]);

                return;
            }
        }
    }

    protected function hookOpened(StreamModeInterface|Stringable|string $mode): void
    {
        $this->hook(function(StreamObserverInterface $observer) use($mode) {
            $observer->opened($mode);
        });
    }

    protected function hookClosed(): void
    {
        $this->hook(function(StreamObserverInterface $observer) {
            $observer->closed();
        });
    }

    protected function hookLocked(mixed $operation): void
    {
        $this->hook(function(StreamObserverInterface $observer) use($operation) {
            $observer->locked($operation);
        });
    }

    protected function hookPositionChanged(int $offset, int $whence): void
    {
        $this->hook(function(StreamObserverInterface $observer) use($offset, $whence) {
            $observer->positionChanged($offset, $whence);
        });
    }

    protected function hookTruncated(int $size): void
    {
        $this->hook(function(StreamObserverInterface $observer) use($size) {
            $observer->truncated($size);
        });
    }

    protected function hookRead(int $count, string $data): void
    {
        $this->hook(function(StreamObserverInterface $observer) use($count, $data) {
            $observer->read($count, $data);
        });
    }

    protected function hookWritten(string $data): void
    {
        $this->hook(function(StreamObserverInterface $observer) use($data) {
            $observer->written($data);
        });
    }

    protected function hookFlushed(): void
    {
        $this->hook(function(StreamObserverInterface $observer) {
            $observer->flushed();
        });
    }

    private function hook(callable $hook): void
    {
        foreach($this->observers as $observer) {
            $hook($observer);
        }
    }

}