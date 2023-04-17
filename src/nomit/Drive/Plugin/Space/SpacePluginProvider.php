<?php

namespace nomit\Drive\Plugin\Space;

use nomit\Drive\Exception\InvalidArgumentException;
use nomit\Drive\Plugin\AbstractPluginProvider;
use nomit\Drive\Plugin\DirectoryPluginProviderInterface;
use nomit\Drive\Plugin\PluginInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;

final class SpacePluginProvider extends AbstractPluginProvider implements DirectoryPluginProviderInterface
{

    public function getName(): string
    {
        return 'space';
    }

    public function providesDirectory(DirectoryInterface $directory): bool
    {
        return $directory->getPathname(false)->getLocalAdapter() instanceof SpaceAwareAdapterInterface;
    }

    public function fromDirectory(DirectoryInterface $directory): PluginInterface
    {
        if($this->providesDirectory($directory)) {
            return new SpaceDirectoryPlugin($directory);
        }

        throw new InvalidArgumentException(sprintf('The supplied directory object, representing the directory with a pathname "%s", does not implement the "%s" interface, and so cannot be provided the "%s" plugin.', $directory->getPathname(), SpaceAwareAdapterInterface::class, SpaceDirectoryPlugin::class));
    }

}