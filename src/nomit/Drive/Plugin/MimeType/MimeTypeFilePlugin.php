<?php

namespace nomit\Drive\Plugin\MimeType;

use nomit\Drive\Plugin\AbstractFilePlugin;
use nomit\Drive\Utility\MimeType\MimeTypeUtility;

final class MimeTypeFilePlugin extends AbstractFilePlugin
{

    public function getName(): string
    {
        return 'file.mimetype';
    }

    public function getMimeName(): string
    {
        $adapter = $this->getFile()->getPathname(false)->getLocalAdapter();

        if($adapter instanceof MimeTypeAwareAdapterInterface) {
            return $adapter->getMimeName($this->getFile()->getPathname(false));
        }

        return MimeTypeUtility::getMimeNameFromFile($this->getFile());
    }

    public function getMimeType(): string
    {
        $adapter = $this->getFile()->getPathname(false)->getLocalAdapter();

        if($adapter instanceof MimeTypeAwareAdapterInterface) {
            return $adapter->getMimeType($this->getFile()->getPathname(false));
        }

        return MimeTypeUtility::getMimeTypeFromFile($this->getFile());
    }

    public function getMimeEncoding(): string
    {
        $adapter = $this->getFile()->getPathname(false)->getLocalAdapter();

        if($adapter instanceof MimeTypeAwareAdapterInterface) {
            return $adapter->getMimeEncoding($this->getFile()->getPathname(false));
        }

        return MimeTypeUtility::getMimeEncodingByFile($this->getFile());
    }

}