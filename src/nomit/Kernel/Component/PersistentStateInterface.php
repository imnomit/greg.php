<?php

namespace nomit\Kernel\Component;

interface PersistentStateInterface
{

    public function loadState(array $parameters): void;

    public function saveState(array &$parameters): void;

}