<?php

namespace nomit\Client\Support;

use nomit\Client\Response\AsynchronousResponse;
use nomit\Client\Response\ResponseInterface;
use nomit\Client\Response\ResponseStream;
use nomit\Client\Response\ResponseStreamInterface;

trait AsynchronousDecoratorTrait
{

    use DecoratorTrait;

    /**
     * {@inheritdoc}
     *
     * @return AsynchronousResponse
     */
    abstract public function request(string $method, string $url, array $options = []): ResponseInterface;

    /**
     * {@inheritdoc}
     */
    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof AsynchronousResponse) {
            $responses = [$responses];
        } elseif (!is_iterable($responses)) {
            throw new \TypeError(sprintf('"%s()" expects parameter 1 to be an iterable of AsyncResponse objects, "%s" given.', __METHOD__, get_debug_type($responses)));
        }

        return new ResponseStream(AsynchronousResponse::stream($responses, $timeout, static::class));
    }

}