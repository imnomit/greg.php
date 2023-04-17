<?php

namespace nomit\Work\Results;

use nomit\Utility\Bag\BagInterface;
use nomit\Work\CallbackInterface;

interface ResultsInterface extends BagInterface
{

    public function getProcess(): ?CallbackInterface;

    public function getResults(): mixed;

}