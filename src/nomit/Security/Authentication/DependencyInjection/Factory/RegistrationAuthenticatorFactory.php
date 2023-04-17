<?php

namespace nomit\Security\DependencyInjection\Factory;

use nomit\DependencyInjection\ContainerBuilder;
use nomit\Security\Authentication\Authenticator\RegistrationAuthenticator;
use nomit\Security\Authentication\DependencyInjection\Factory\AbstractSecurityFactory;
use nomit\Security\Authentication\DependencyInjection\Factory\AuthenticatorFactoryInterface;

final class RegistrationAuthenticatorFactory extends AbstractSecurityFactory implements AuthenticatorFactoryInterface
{

    public const PRIORITY = -40;

    public function getPriority(): int
    {
        return self::PRIORITY;
    }

    public function getKey(): string
    {
        return 'registration';
    }

    public function getPosition(): string
    {
        return 'registration';
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, \stdClass $config, string $userProviderId, ?string $defaultEntryPoint): string|array
    {
        $container->addDefinition($authenticatorId = 'authentication.authenticator.registration')
            ->setType(RegistrationAuthenticator::class)
            ->setArguments([
                $container->getDefinition('registration.token.storage'),
                $container->getDefinition($userProviderId),
                $this->createSuccessfulAuthenticationHandler($container, $firewallName, $config),
                $this->createFailedAuthenticationHandler($container, $firewallName, $config),
                $config,
                $container->getDefinition('event_dispatcher')
            ]);

        return [$authenticatorId, null];
    }

}