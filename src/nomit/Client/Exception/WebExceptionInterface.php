<?php

namespace nomit\Client\Exception;

use nomit\Client\Response\ResponseInterface;

interface WebExceptionInterface extends ExceptionInterface
{

    public function getResponse(): ResponseInterface;

}