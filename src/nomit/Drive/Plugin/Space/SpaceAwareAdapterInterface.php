<?php

namespace nomit\Drive\Plugin\Space;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Pathname\PathnameInterface;

interface SpaceAwareAdapterInterface extends AdapterInterface
{

    public function getTotalSpace(PathnameInterface $pathname): ?float;

    public function getAvailableSpace(PathnameInterface $pathname): ?float;

}