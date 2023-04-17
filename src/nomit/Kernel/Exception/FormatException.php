<?php

namespace nomit\Kernel\Exception;

use nomit\Exception\LogicException;
use nomit\Kernel\Exception\FormatExceptionContext;

class FormatException extends LogicException implements ExceptionInterface
{

    private $context;

    public function __construct(string $message, FormatExceptionContext $context, int $code = 0, \Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct(sprintf("%s in \"%s\" at line %d.\n%s", $message, $context->getPath(), $context->getLineno(), $context->getDetails()), $code, $previous);
    }

    public function getContext(): FormatExceptionContext
    {
        return $this->context;
    }

}