<?php

namespace nomit\Drive\Exception\Adapter;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Exception\ExceptionInterface;

interface AdapterExceptionInterface extends ExceptionInterface
{

    public function getAdapter(): AdapterInterface;

}