<?php

namespace nomit\Drive\Plugin\Hash;

use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Exception\Plugin\PluginException;
use nomit\Drive\Plugin\AbstractFilePlugin;
use nomit\Drive\Plugin\AbstractResourcePlugin;
use nomit\Utility\Callback\CallbackUtility;

final class HashFilePlugin extends AbstractFilePlugin
{

    public function getName(): string
    {
        return 'file.hash';
    }

    public function getHash(string $algorithm, bool $binary = false): string
    {
        $adapter = $this->resource->getPathname(false)->getLocalAdapter();

        if($adapter instanceof HashAwareAdapterInterface) {
            return $adapter->getHash($this->resource->getPathname(false), $algorithm, $binary);
        }

        $file = $this->getFile();

        return CallbackUtility::callSafely(
            function() use($algorithm, $file, $binary) {
                return hash(
                    $algorithm,
                    $file->read(),
                    $binary
                );
            },
            [],
            PluginException::class,
            ExceptionCodeEnumeration::PLUGIN_EXCEPTION,
            'An error occurred while attempting to calculate the "%s" hash of the file, pathname "%s", represented by the resource object assigned to the "%s" plugin.',
            $algorithm,
            $file->getPathname(),
            get_class($this)
        );
    }

}