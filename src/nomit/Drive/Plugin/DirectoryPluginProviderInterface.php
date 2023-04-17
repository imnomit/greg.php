<?php

namespace nomit\Drive\Plugin;

use nomit\Drive\Resource\Directory\DirectoryInterface;

interface DirectoryPluginProviderInterface extends PluginProviderInterface
{

    public function providesDirectory(DirectoryInterface $directory): bool;

    public function fromDirectory(DirectoryInterface $directory): PluginInterface;

}