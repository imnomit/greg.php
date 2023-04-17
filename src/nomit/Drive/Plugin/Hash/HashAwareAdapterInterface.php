<?php

namespace nomit\Drive\Plugin\Hash;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Pathname\PathnameInterface;

interface HashAwareAdapterInterface extends AdapterInterface
{

    public function getHash(PathnameInterface $pathname, string $algorithm, bool $binary = false): string;

}