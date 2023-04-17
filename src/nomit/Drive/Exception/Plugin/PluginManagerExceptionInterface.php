<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Exception\ExceptionInterface;
use nomit\Drive\Plugin\PluginManagerInterface;

interface PluginManagerExceptionInterface extends ExceptionInterface
{

    public function getPluginManager(): PluginManagerInterface;

}