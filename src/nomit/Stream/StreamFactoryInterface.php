<?php

namespace nomit\Stream;

interface StreamFactoryInterface extends \Psr\Http\Message\StreamFactoryInterface
{

    public function createStreamFromIterator(\Traversable $resource): \Psr\Http\Message\StreamInterface;

    public function createStreamFromCallback(\Closure|callable $resource): \Psr\Http\Message\StreamInterface;

}