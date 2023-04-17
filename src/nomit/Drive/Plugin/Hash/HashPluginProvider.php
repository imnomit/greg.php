<?php

namespace nomit\Drive\Plugin\Hash;

use nomit\Drive\Exception\Plugin\PluginProviderException;
use nomit\Drive\Plugin\AbstractPlugin;
use nomit\Drive\Plugin\AbstractPluginProvider;
use nomit\Drive\Plugin\PluginInterface;
use nomit\Drive\Plugin\FilePluginProviderInterface;
use nomit\Drive\Plugin\ResourcePluginInterface;
use nomit\Drive\FileSystemInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Resource\ResourceInterface;

final class HashPluginProvider extends AbstractPluginProvider implements FilePluginProviderInterface
{

    public function getName(): string
    {
        return 'hash';
    }

    public function providesFile(FileInterface $file): bool
    {
        return true;
    }

    public function fromFile(FileInterface $file): PluginInterface
    {
        return new HashFilePlugin($file);
    }

}