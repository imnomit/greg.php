<?php

namespace nomit\Security\Registration\Handler;

use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\Registration\Event\RegistrationEvent;
use nomit\Security\Registration\Exception\RegistrationExceptionInterface;
use nomit\Web\Response\ResponseInterface;

interface FailedRegistrationHandlerInterface
{

    public function onRegistrationFailure(RegistrationEvent $event, RegistrationExceptionInterface|AuthenticationException $exception): ?ResponseInterface;

}