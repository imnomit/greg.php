<?php

namespace nomit\Web\Request;

use Psr\Http\Message\RequestInterface;

/**
 * Interface RequestAwareInterface
 * @package nomit\Web\Request
 */
interface RequestAwareInterface
{

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request): self;

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

}