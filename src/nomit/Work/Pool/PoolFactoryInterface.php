<?php

namespace nomit\Work\Pool;

interface PoolFactoryInterface
{

    public function createPool(): PoolInterface;

    public function createFixedPool(int $maximumProcesses = 4): PoolInterface;

    public function createParallelPool($callback, int $maximumProcesses = 4): PoolInterface;

    public function createSinglePool(): PoolInterface;

}