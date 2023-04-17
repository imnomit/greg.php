<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Plugin\PluginProviderInterface;

class SupportlessPluginProviderException extends PluginProviderException
{

    public function __construct(
        PluginProviderInterface $provider,
        string                  $pluginClassName,
        ?\Throwable             $previous = null
    )
    {
        parent::__construct(
            $provider,
            sprintf('The "%s" plugin provider does not support or provide a "%s"-type plugin.', $provider, $pluginClassName),
            $previous
        );
    }

}