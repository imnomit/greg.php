<?php

namespace nomit\Kernel\Event;

use nomit\Kernel\KernelInterface;
use nomit\Web\Request\Request;

/**
 * Class ExceptionEvent
 * @package nomit\Events
 */
class ExceptionEvent extends RequestEvent
{

    /**
     * @var \Throwable|null
     */
    private ?\Throwable $throwable = null;

    /**
     * @var bool
     */
    private $allowCustomResponseCode = false;

    /**
     * ExceptionEvent constructor.
     * @param KernelInterface $kernel
     * @param Request $request
     * @param int $requestType
     * @param \Throwable $e
     */
    public function __construct(KernelInterface $kernel, Request $request, int $requestType, \Throwable $e)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->setThrowable($e);
    }

    /**
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * Replaces the thrown exception.
     *
     * This exception will be thrown if no response is set in the event.
     */
    public function setThrowable(\Throwable $exception): void
    {
        $this->throwable = $exception;
    }

    /**
     * Mark the event as allowing a custom response code.
     */
    public function allowCustomResponseCode(): void
    {
        $this->allowCustomResponseCode = true;
    }

    /**
     * Returns true if the event allows a custom response code.
     */
    public function isAllowingCustomResponseCode(): bool
    {
        return $this->allowCustomResponseCode;
    }

}