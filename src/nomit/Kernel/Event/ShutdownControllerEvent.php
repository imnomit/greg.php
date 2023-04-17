<?php

namespace nomit\Kernel\Event;

use nomit\Kernel\Component\ControllerInterface;
use Psr\Http\Message\ResponseInterface;

final class ShutdownControllerEvent extends ControllerEvent
{

    public function __construct(
        ControllerInterface $controller,
        private ResponseInterface $response
    )
    {
        parent::__construct($controller);
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

}