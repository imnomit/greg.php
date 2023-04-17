<?php

namespace nomit\Stream;

use nomit\Utility\Concern\Stringable;

interface ObservableStreamInterface extends StreamInterface
{

    public function addObserver(StreamObserverInterface $observer): self;

    public function removeObserver(StreamObserverInterface $observer): void;

}