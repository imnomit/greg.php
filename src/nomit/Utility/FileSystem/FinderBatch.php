<?php

namespace nomit\Utility\FileSystem;

use nomit\Utility\Object\SmartObjectTrait;

final class FinderBatch
{

    use SmartObjectTrait;

    public array $find = [];
    public array $in = [];
    public array $filters = [];
    public array $recurseFilters = [];

}