<?php

namespace nomit\Security\Authentication\DependencyInjection\Factory;

use nomit\DependencyInjection\ContainerBuilder;

interface EventListeningAuthenticatorFactoryInterface extends AuthenticatorFactoryInterface
{

    public function createEventListeners(ContainerBuilder $container, string $firewallName): void;

}