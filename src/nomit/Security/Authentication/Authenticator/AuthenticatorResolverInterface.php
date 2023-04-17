<?php

namespace nomit\Security\Authentication\Authenticator;

use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

interface AuthenticatorResolverInterface
{

    public function authenticate(RequestInterface $request, ResponseInterface $response = null): ?ResponseInterface;

}