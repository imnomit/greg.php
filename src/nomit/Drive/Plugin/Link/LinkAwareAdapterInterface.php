<?php

namespace nomit\Drive\Plugin\Link;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Pathname\PathnameInterface;

interface LinkAwareAdapterInterface extends AdapterInterface
{

    public function isLink(PathnameInterface $pathname): bool;

    public function getTarget(PathnameInterface $pathname): ?string;

}