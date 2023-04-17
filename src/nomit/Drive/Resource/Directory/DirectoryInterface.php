<?php

namespace nomit\Drive\Resource\Directory;

use nomit\Drive\Resource\ResourceInterface;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;

interface DirectoryInterface extends ResourceInterface
{

    public function list(int|Integerable|string|Stringable|\Closure|callable $filter, mixed ...$arguments): array;

}