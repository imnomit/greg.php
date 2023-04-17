<?php

namespace nomit\Drive\Plugin\Link;

use nomit\Drive\Exception\InvalidArgumentException;
use nomit\Drive\Plugin\AbstractPlugin;
use nomit\Drive\Plugin\AbstractPluginProvider;
use nomit\Drive\Plugin\PluginInterface;
use nomit\Drive\Plugin\FilePluginInterface;
use nomit\Drive\Plugin\FilePluginProviderInterface;
use nomit\Drive\Resource\File\FileInterface;

final class LinkPluginProvider extends AbstractPluginProvider implements FilePluginProviderInterface
{

    public function getName(): string
    {
        return 'link';
    }

    public function providesFile(FileInterface $file): bool
    {
        return $file->getPathname(false)->getLocalAdapter() instanceof LinkAwareAdapterInterface;
    }

    public function fromFile(FileInterface $file): PluginInterface
    {
        if($this->providesFile($file)) {
            return new LinkFilePlugin($file);
        }

        throw new InvalidArgumentException(sprintf('The supplied file object, representing the file with a pathname "%s", does not implement the "%s" interface, and so cannot be provided the "%s" plugin.', $file->getPathname(), LinkAwareAdapterInterface::class, LinkFilePlugin::class));
    }

}