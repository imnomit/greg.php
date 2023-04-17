<?php

namespace nomit\Security\PasswordReset\DependencyInjection;

use nomit\Database\ConnectionInterface;
use nomit\Database\ExplorerInterface;
use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\Container;
use nomit\DependencyInjection\ContainerBuilder;
use nomit\DependencyInjection\Definitions\Definition;
use nomit\DependencyInjection\Exception\InvalidConfigurationException;
use nomit\DependencyInjection\Exception\MissingServiceException;
use nomit\Schema\Expect;

final class PasswordResetExtension extends CompilerExtension
{

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'password_hasher' => Expect::scalar(),
            'catch_exceptions' => Expect::bool(),
            'token' => Expect::structure([
                'ttl' => Expect::int(),
                'session_token_name' => Expect::scalar(),
                'csrf_token_name' => Expect::scalar(),
                'persistence' => Expect::structure([
                    'service' => Expect::scalar('database'),
                    'database' => Expect::structure([
                        'table' => Expect::scalar('user_password_resets')
                    ])
                ])
            ]),
            'request' => Expect::structure([
                'path' => Expect::scalar(),
                'mail' => Expect::structure([
                    'from' => Expect::scalar(),
                    'subject' => Expect::scalar()
                ])
            ]),
            'validate' => Expect::structure([
                'path' => Expect::scalar(),
            ]),
            'perform' => Expect::structure([
                'path' => Expect::scalar()
            ])
        ]);
    }

    public function loadConfiguration()
    {
        $this->loadDefinitionsFromConfig(
            $this->loadFromFile(
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.neon'
            )
        );
    }

    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();
        $config = $this->config;

        $tokenPersistence = $this->registerTokenPersistence($container, $config->token->persistence);
        $tokenManager = $this->registerTokenManager($container, $tokenPersistence, $config);
        $mailer = $this->registerMailer($container);

        $this->registerPasswordHashingEventListener($container);
        $this->registerPerformPasswordResetEventListener($container, $config);
        $this->registerRequestPasswordResetEventListener($container, $tokenManager, $mailer, $config);
        $this->registerValidatePasswordResetEventListener($container, $tokenManager, $config);
    }

    private function registerTokenManager(ContainerBuilder $container, Definition $tokenPersistence, \stdClass $config): Definition
    {
        if(!$container->hasDefinition($serviceId = $this->prefix('token.manager'))) {
            throw new MissingServiceException(sprintf('The password reset token manager service with the service ID "%s" is missing from the service container.', $serviceId));
        }

        return $container->getDefinition($serviceId)
            ->setArguments([
                $container->getDefinition('cryptography.hasher'),
                $tokenPersistence,
                $config
            ]);
    }

    private function registerTokenPersistence(ContainerBuilder $container, \stdClass $config): Definition
    {
        $serviceName = $config->service;
        $serviceId = $this->prefix('token.persistence.' . $serviceName);
        $serviceConfig = $config->$serviceName ?? null;

        if(!$serviceConfig) {
            throw new InvalidConfigurationException(sprintf('Configuration parameters are missing for the password reset token persistence service with the service ID "%s".', $serviceName));
        }

        foreach($container->findByTag('security.password_reset.token.persistence') as $subjectServiceId => $tagValue) {
            if($subjectServiceId === $serviceId) {
                $definition = $container->getDefinition($serviceId);

                return match($serviceName) {
                    'database' => $definition->setArguments([
                        $container->getDefinitionByType(ConnectionInterface::class),
                        $container->getDefinitionByType(ExplorerInterface::class),
                        $serviceConfig->table,
                        $container->getDefinition('logger')
                    ]),
                    default => throw new InvalidConfigurationException(sprintf('The configured password reset token persistence service, service ID "%s", is not supported.', $serviceName))
                };
            }
        }

        throw new InvalidConfigurationException(sprintf('The configured password reset token persistence service, service ID "%s", does not exist in the service container.', $serviceName));
    }

    private function registerMailer(ContainerBuilder $container): Definition
    {
        if(!$container->hasDefinition($serviceId = $this->prefix('mail.mailer'))) {
            throw new MissingServiceException(sprintf('The password reset mailer service with a service ID "%s" is missing from the service container.', $serviceId));
        }

        return $container->getDefinition($serviceId)
            ->setArguments([
                $container->getDefinition('mail.mailer'),
                $container->parameters['paths']['resources']
            ]);
    }

    private function registerPasswordHashingEventListener(ContainerBuilder $container): Definition
    {
        return $container->getDefinition($this->prefix('event_listener.password_hashing'))
            ->setArguments([
                $container->getDefinition('logger')
            ]);
    }

    private function registerPerformPasswordResetEventListener(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->getDefinition($this->prefix('event_listener.perform'))
            ->setArguments([
                $container->getDefinition('csrf.manager'),
                $container->getDefinition('session'),
                $this->createPasswordHasher($container, $config->password_hasher),
                $container->parameters['user_providers'],
                $container->getDefinitionByType(Container::class),
                $config,
                $container->getDefinition('event_dispatcher'),
                $container->getDefinition('logger')
            ]);
    }

    private function createPasswordHasher(ContainerBuilder $container, string $serviceName): Definition
    {
        $serviceId = 'cryptography.password.hasher.' . $serviceName;

        if(!$container->hasDefinition($serviceId)) {
            throw new InvalidConfigurationException(sprintf('No password hasher with the configured service name, "%s", exists.', $serviceName));
        }

        return $container->getDefinition($serviceId);
    }

    private function registerRequestPasswordResetEventListener(ContainerBuilder $container, Definition $tokenManager, Definition $mailer, \stdClass $config): Definition
    {
        return $container->getDefinition($this->prefix('event_listener.request'))
            ->setArguments([
                $tokenManager,
                $mailer,
                $config,
                $container->parameters['user_providers'],
                $container->getDefinitionByType(Container::class),
                $container->getDefinition('logger')
            ]);
    }

    private function registerValidatePasswordResetEventListener(ContainerBuilder $container, Definition $tokenManager, \stdClass $config): Definition
    {
        return $container->getDefinition($this->prefix('event_listener.validate'))
            ->setArguments([
                $tokenManager,
                $container->getDefinition('csrf.manager'),
                $container->getDefinition('session'),
                $config,
                $container->getDefinition('logger')
            ]);
    }

}