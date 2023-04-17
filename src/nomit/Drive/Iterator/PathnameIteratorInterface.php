<?php

namespace nomit\Drive\Iterator;

use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Concern\Arrayable;

interface PathnameIteratorInterface extends \Iterator,
    \SeekableIterator,
    Arrayable
{

    public function getBitmask(): ?BitmaskInterface;

    public function getGlobFilters(): array;

    public function getCallableFilters(): array;

    public function getGlobSearchPatterns(): array;

}