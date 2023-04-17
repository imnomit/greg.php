<?php

namespace nomit\Security\Authorization\Exception;

use nomit\Exception\RuntimeException;

class AccessDeniedException extends AuthorizationException implements ExceptionInterface
{

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var mixed
     */
    private $subject;

    /**
     * AccessDeniedException constructor.
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = 'Access Denied.', \Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array|string $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = (array) $attributes;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

}