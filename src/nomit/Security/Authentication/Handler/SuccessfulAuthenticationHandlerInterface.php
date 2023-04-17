<?php

namespace nomit\Security\Authentication\Handler;

use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

interface SuccessfulAuthenticationHandlerInterface
{

    public function onSuccessfulAuthentication(RequestInterface $request, TokenInterface $token): ResponseInterface;

}