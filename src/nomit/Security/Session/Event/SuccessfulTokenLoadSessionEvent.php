<?php

namespace nomit\Security\Session\Event;

use nomit\EventDispatcher\Event;
use nomit\Security\Session\SessionInterface;
use nomit\Security\Session\Token\TokenInterface;

class SuccessfulTokenLoadSessionEvent extends SessionEvent
{

    protected TokenInterface $token;

    public function __construct(SessionInterface $session, TokenInterface $token)
    {
        $this->token = $token;

        parent::__construct($session);
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }

}