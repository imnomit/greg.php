<?php

namespace nomit\Drive\Exception\Adapter;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Exception\Exception;
use nomit\Drive\Exception\ExceptionCodeEnumeration;

class AdapterException extends Exception implements AdapterExceptionInterface
{

    public function __construct(
        private readonly AdapterInterface $adapter,
        string                            $message = '',
        \Throwable                        $previous = null
    )
    {
        parent::__construct($message, ExceptionCodeEnumeration::OPERATION_EXCEPTION, $previous);
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

}