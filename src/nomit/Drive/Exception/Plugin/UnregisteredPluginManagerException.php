<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Plugin\PluginManagerInterface;

class UnregisteredPluginManagerException extends PluginManagerException
{

    public function __construct(
        protected readonly string $plugin,
        PluginManagerInterface    $manager,
        ?\Throwable               $previous = null
    )
    {
        parent::__construct(
            $manager,
            sprintf(
                'The plugin referenced by the supplied name, "%s", has not been registered with the "%s" plugin manager.',
                $this->plugin,
                get_class($manager)
            ),
            $previous
        );
    }

    public function getPlugin(): string
    {
        return $this->plugin;
    }

}