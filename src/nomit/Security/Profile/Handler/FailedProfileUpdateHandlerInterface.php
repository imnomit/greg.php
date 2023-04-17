<?php

namespace nomit\Security\Profile\Handler;

use nomit\Exception\ExceptionInterface;
use nomit\Security\Profile\EditableProfileInterface;
use nomit\Web\Response\ResponseInterface;

interface FailedProfileUpdateHandlerInterface
{

    public function onFailedProfileUpdate(EditableProfileInterface $profile, ExceptionInterface $exception): ResponseInterface;

}