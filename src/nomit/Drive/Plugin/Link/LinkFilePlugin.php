<?php

namespace nomit\Drive\Plugin\Link;

use nomit\Drive\Plugin\AbstractFilePlugin;

final class LinkFilePlugin extends AbstractFilePlugin
{

    public function getName(): string
    {
        return 'file.link';
    }

    public function isLink(): bool
    {
        $adapter = $this->getFile()->getPathname(false)->getLocalAdapter();

        if($adapter instanceof LinkAwareAdapterInterface) {
            return $adapter->isLink($this->getFile()->getPathname(false));
        }

        return false;
    }

    public function getTarget(): ?string
    {
        $adapter = $this->getFile()->getPathname(false)->getLocalAdapter();

        if($adapter instanceof LinkAwareAdapterInterface) {
            return $adapter->getTarget($this->getFile()->getPathname(false));
        }

        return null;
    }

}