<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\ExceptionInterface;

interface DirectoryExceptionInterface extends ExceptionInterface
{

    public function getPathname(): string;

}