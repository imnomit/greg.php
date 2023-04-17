<?php

namespace nomit\Drive\Stream;

use nomit\Drive\Resource\File\FileInterface;

interface StreamInterface extends \nomit\Stream\StreamInterface
{

    public function getFile(): FileInterface;

}