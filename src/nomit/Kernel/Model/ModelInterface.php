<?php

namespace nomit\Kernel\Model;

use nomit\Cache\Cache;
use nomit\Database\ConnectionInterface;
use nomit\Database\ExplorerInterface;

interface ModelInterface
{

    public function getDatabase(): ConnectionInterface;

    public function getExplorer(): ExplorerInterface;

    public function getCache(): ?Cache;

}