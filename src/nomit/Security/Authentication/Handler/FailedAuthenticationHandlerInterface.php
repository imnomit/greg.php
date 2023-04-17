<?php

namespace nomit\Security\Authentication\Handler;

use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

interface FailedAuthenticationHandlerInterface
{

    public function onFailedAuthentication(RequestInterface $request, AuthenticationException $exception): ResponseInterface;

}