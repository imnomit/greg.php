<?php

namespace nomit\Client\Response;

use nomit\Client\Chunk\ChunkInterface;

interface ResponseStreamInterface extends \Iterator
{

    public function key(): ResponseInterface;

    public function current(): ChunkInterface;

}