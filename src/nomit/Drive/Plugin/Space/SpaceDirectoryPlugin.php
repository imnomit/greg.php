<?php

namespace nomit\Drive\Plugin\Space;

use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Exception\Plugin\PluginException;
use nomit\Drive\Plugin\AbstractDirectoryPlugin;
use nomit\Drive\Plugin\AbstractFilePlugin;
use nomit\Utility\Callback\CallbackUtility;

final class SpaceDirectoryPlugin extends AbstractDirectoryPlugin
{

    public function getName(): string
    {
        return 'directory.space';
    }

    public function getTotalSpace(): ?float
    {
        $adapter = $this->getDirectory()->getPathname(false)->getLocalAdapter();

        if($adapter instanceof SpaceAwareAdapterInterface) {
            return $adapter->getTotalSpace($this->getDirectory()->getPathname(false));
        }

        $directory = $this->getDirectory();

        return CallbackUtility::callSafely(
            function() use($directory) {
                return disk_total_space($directory->getPathname());
            },
            [],
            PluginException::class,
            ExceptionCodeEnumeration::PLUGIN_EXCEPTION,
            'An error occurred while attempting to calculate the total disk space of the directory, pathname "%s", represented by the "%s" object assigned to the "%s" plugin.',
            $directory->getPathname(),
            get_class($directory),
            get_class($this)
        );
    }

    public function getAvailableSpace(): ?float
    {
        $adapter = $this->getDirectory()->getPathname(false)->getLocalAdapter();

        if($adapter instanceof SpaceAwareAdapterInterface) {
            return $adapter->getAvailableSpace($this->getDirectory()->getPathname(false));
        }

        $directory = $this->getDirectory();

        return CallbackUtility::callSafely(
            function() use($directory) {
                return disk_free_space($directory->getPathname());
            },
            [],
            PluginException::class,
            ExceptionCodeEnumeration::PLUGIN_EXCEPTION,
            'An error occurred while attempting to calculate the available disk space of the directory, pathname "%s", represented by the "%s" object assigned to the "%s" plugin.',
            $directory->getPathname(),
            get_class($directory),
            get_class($this)
        );
    }

}