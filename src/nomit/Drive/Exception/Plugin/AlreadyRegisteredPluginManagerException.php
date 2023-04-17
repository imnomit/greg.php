<?php

namespace nomit\Drive\Exception\Plugin;

use nomit\Drive\Plugin\PluginManagerInterface;

class AlreadyRegisteredPluginManagerException extends PluginManagerException
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
                'The supplied plugin, named "%s", has already been previously registered to the "%s" plugin manager.',
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