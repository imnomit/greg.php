<?php

namespace nomit\Process\Signal;

interface SignalInterface
{

    public function __invoke(int $signal);

}