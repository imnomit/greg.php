<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\File\FileInterface;

interface FilePluginProviderInterface extends PluginProviderInterface
{

    public function providesFile(FileInterface $file): bool;

    public function fromFile(FileInterface $file): PluginInterface;

}