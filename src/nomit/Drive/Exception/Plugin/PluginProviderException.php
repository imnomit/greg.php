<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Exception\Exception;
use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Plugin\PluginProviderInterface;

class PluginProviderException extends Exception implements PluginProviderExceptionInterface
{

    public function __construct(
        protected readonly PluginProviderInterface $provider,
        string                                     $message = "",
        ?\Throwable                                $previous = null
    )
    {
        parent::__construct($message, ExceptionCodeEnumeration::PLUGIN_EXCEPTION, $previous);
    }

    public function getPluginProvider(): PluginProviderInterface
    {
        return $this->provider;
    }

}