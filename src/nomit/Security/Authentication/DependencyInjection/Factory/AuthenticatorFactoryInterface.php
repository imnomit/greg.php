<?php

namespace nomit\Security\Authentication\DependencyInjection\Factory;

use nomit\DependencyInjection\ContainerBuilder;

interface AuthenticatorFactoryInterface
{

    public function getKey(): string;

    public function createAuthenticator(
        ContainerBuilder $container,
        string $firewallName,
        \stdClass $config,
        string $userProviderId,
        ?string $defaultEntryPoint
    ): string|array;

}