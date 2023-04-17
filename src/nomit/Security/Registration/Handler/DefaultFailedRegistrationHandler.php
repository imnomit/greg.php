<?php

namespace nomit\Security\Registration\Handler;

use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\Registration\Event\RegistrationEvent;
use nomit\Security\Registration\Exception\RegistrationExceptionInterface;
use nomit\Web\Response\JsonResponse;
use nomit\Web\Response\ResponseInterface;

final class DefaultFailedRegistrationHandler implements FailedRegistrationHandlerInterface
{

    public function onRegistrationFailure(RegistrationEvent $event, RegistrationExceptionInterface|AuthenticationException $exception): ?ResponseInterface
    {
        return new JsonResponse([
            'success' => false,
            'request' => $event->getRequest()->toArray(),
            'registration' => $event->getRegistration()->toArray(),
            'exception' => $exception ? $this->serializeException($exception) : null,
        ], null, 'failure');
    }

    private function serializeException(RegistrationExceptionInterface $exception): array
    {
        return [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'trace' => $exception->getTrace(),
            'previous' => ($previous = $exception->getPrevious()) ? $this->serializeException($previous) : null,
        ];
    }

}