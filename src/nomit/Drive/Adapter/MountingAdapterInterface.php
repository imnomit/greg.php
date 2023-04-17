<?php

namespace nomit\Drive\Adapter;

use nomit\Drive\Pathname\PathnameInterface;
use nomit\Utility\Concern\Stringable;

interface MountingAdapterInterface extends AdapterInterface
{

    public function mount(string|PathnameInterface|Stringable $pathname, AdapterInterface $adapter): self;

    public function isMounted(string|PathnameInterface|Stringable $pathname): bool;

    public function unmount(string|PathnameInterface|Stringable $pathname): void;

}