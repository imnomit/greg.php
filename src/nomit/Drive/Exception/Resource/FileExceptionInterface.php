<?php

namespace nomit\Drive\Exception\Resource;

use nomit\Drive\Exception\ExceptionInterface;

interface FileExceptionInterface extends ExceptionInterface
{

    public function getFilename(): string;

}