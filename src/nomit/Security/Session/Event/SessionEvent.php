<?php

namespace nomit\Security\Session\Event;

use nomit\Dumper\Dumper;
use nomit\EventDispatcher\Event;
use nomit\Security\Session\SessionInterface;

class SessionEvent extends Event
{

    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

}