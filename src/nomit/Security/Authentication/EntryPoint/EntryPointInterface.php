<?php

namespace nomit\Security\Authentication\EntryPoint;

use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

interface EntryPointInterface
{

    public function respond(RequestInterface $request, ?AuthenticationException $exception = null): ResponseInterface;

}