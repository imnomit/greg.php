<?php

namespace nomit\Kernel\Exception;

use nomit\Web\Response\Response;

class ForbiddenRequestException extends BadRequestException
{

    protected $code = Response::HTTP_FORBIDDEN;

}