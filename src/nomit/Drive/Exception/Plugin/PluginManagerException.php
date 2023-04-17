<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Exception\Exception;
use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Plugin\PluginManagerInterface;

class PluginManagerException extends Exception implements PluginManagerExceptionInterface
{

    public function __construct(
        protected readonly PluginManagerInterface $manager,
        string                                    $message = "",
        ?\Throwable                               $previous = null
    )
    {
        parent::__construct($message, ExceptionCodeEnumeration::PLUGIN_EXCEPTION, $previous);
    }

    public function getPluginManager(): PluginManagerInterface
    {
        return $this->manager;
    }

}