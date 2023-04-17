<?php

namespace nomit\Console\Command;

interface SignallingCommand extends CommandInterface
{

    public function getSubscribedSignals(): array;

    public function signal(int $signal): void;

}