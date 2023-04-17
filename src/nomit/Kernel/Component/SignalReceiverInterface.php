<?php

namespace nomit\Kernel\Component;

interface SignalReceiverInterface
{

    public function receiveSignal(string $signal): void;

}