<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Exception\ExceptionInterface;
use nomit\Drive\Plugin\PluginProviderInterface;

interface PluginProviderExceptionInterface extends ExceptionInterface
{

    public function getPluginProvider(): PluginProviderInterface;

}