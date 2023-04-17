<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Exception\ExceptionInterface;
use nomit\Drive\Plugin\PluginInterface;

interface PluginExceptionInterface extends ExceptionInterface
{

    public function getPlugin(): PluginInterface;

}