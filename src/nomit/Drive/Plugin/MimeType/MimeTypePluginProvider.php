<?php

namespace nomit\Drive\Utility\MimeType;

use nomit\Drive\Plugin\AbstractPluginProvider;
use nomit\Drive\Plugin\MimeType\MimeTypeFilePlugin;
use nomit\Drive\Plugin\PluginInterface;
use nomit\Drive\Plugin\FilePluginProviderInterface;
use nomit\Drive\Resource\File\FileInterface;

final class MimeTypePluginProvider extends AbstractPluginProvider implements FilePluginProviderInterface
{

    public function getName(): string
    {
        return 'mimetype';
    }

    public function providesFile(FileInterface $file): bool
    {
        return true;
    }

    public function fromFile(FileInterface $file): PluginInterface
    {
        return new MimeTypeFilePlugin($file);
    }

}