<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Exception\Exception;
use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Exception\Plugin;
use nomit\Drive\Plugin\PluginInterface;

class PluginException extends Exception implements PluginExceptionInterface
{

    public function __construct(
        protected readonly PluginInterface $plugin,
        string                             $message = "",
        ?\Throwable                        $previous = null
    )
    {
        parent::__construct($message, ExceptionCodeEnumeration::PLUGIN_EXCEPTION, $previous);
    }

    public function getPlugin(): PluginInterface
    {
        return $this->plugin;
    }

}