<?php

namespace nomit\Web\Response;

interface ResponseManagerAwareInterface
{

    public function setResponseManager(ResponseManagerInterface $responseManager): self;

    public function getResponseManager(): ResponseManagerInterface;

}