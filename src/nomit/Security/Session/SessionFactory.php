<?php

namespace nomit\Security\Session;

use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Security\Session\Context\SessionContextInterface;
use nomit\Security\Session\Fingerprint\FingerprintGeneratorInterface;
use nomit\Security\Session\Token\Identity\IdentityGeneratorInterface;
use nomit\Security\Session\Token\Persistence\TokenPersistenceInterface;
use nomit\Security\Session\Token\Storage\TokenStorageInterface;
use Psr\Log\LoggerInterface;

final class SessionFactory implements SessionFactoryInterface
{

    public function __construct(
        private SessionInterface $session
    )
    {
    }

    public function setSession(SessionInterface $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function factory(): SessionInterface
    {
        return $this->getSession();
    }

    public function __invoke()
    {
        return $this->factory();
    }

}