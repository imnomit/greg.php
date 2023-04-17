<?php

namespace Psr\Http\Server;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Handles a server request and produces a response.
 *
 * An HTTP request handler process an HTTP request in order to produce an
 * HTTP response.
 */
interface RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(RequestInterface $request): ResponseInterface;
}
