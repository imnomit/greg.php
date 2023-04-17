<?php

namespace nomit\Security\Profile\Handler;

use nomit\Exception\ExceptionInterface;
use nomit\Security\Profile\EditableProfileInterface;
use nomit\Web\Response\JsonResponse;
use nomit\Web\Response\ResponseInterface;

final class DefaultFailedProfileUpdateHandler implements FailedProfileUpdateHandlerInterface
{

    public function onFailedProfileUpdate(EditableProfileInterface $profile, ExceptionInterface $exception): ResponseInterface
    {
        return new JsonResponse([
            'success' => false,
            'profile' => $profile->toArray(),
            'exception' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]
        ]);
    }

}