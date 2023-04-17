<?php

namespace nomit\Security\Authentication\DependencyInjection\Factory;

use nomit\DependencyInjection\ContainerBuilder;
use nomit\DependencyInjection\Definitions\Definition;
use nomit\Security\Authentication\Handler\CustomFailedAuthenticationHandler;
use nomit\Security\Authentication\Handler\CustomSuccessfulAuthenticationHandler;
use nomit\Security\Authentication\Handler\DefaultFailedAuthenticationHandler;
use nomit\Security\Authentication\Handler\DefaultSuccessfulAuthenticationHandler;

abstract class AbstractSecurityFactory implements SecurityFactoryInterface
{

    protected array $options = [
        'check_path' => '/account/login_check',
        'use_forward' => false,
        'require_previous_session' => false,
        'login_path' => '/account/login',
    ];

    protected array $defaultSuccessHandlerOptions = [
        'always_use_default_target_path' => false,
        'default_target_path' => '/',
        'login_path' => '/account/login',
        'target_path_parameter' => '_target_path',
        'use_referer' => false,
    ];

    protected array $defaultFailureHandlerOptions = [
        'failure_path' => null,
        'failure_forward' => false,
        'login_path' => '/account/login',
        'failure_path_parameter' => '_failure_path',
    ];

    final public function addOption(string $name, mixed $default = null): self
    {
        $this->options[$name] = $default;

        return $this;
    }

    protected function createSuccessfulAuthenticationHandler(
        ContainerBuilder $container,
        string $id,
        \stdClass $config
    ): Definition
    {
        $successHandlerId = $this->getSuccessHandlerId($id);
        $config = get_object_vars($config);
        $options = array_intersect_key($config, $this->defaultSuccessHandlerOptions);

        if(isset($config['success_handler'])) {
            if($config['success_handler'] === 'default') {
                $this->createDefaultSuccessHandler($container, $options);
            }

            return $container->addDefinition($successHandlerId)
                ->setType(CustomSuccessfulAuthenticationHandler::class)
                ->setArguments([
                    $container->getDefinition('authentication.handler.success.' . $config['success_handler']),
                    $options,
                    $id
                ]);
        }

        return $this->createDefaultSuccessHandler($container, $config)
            ->addSetup('setFirewallName', [$id]);
    }

    private function createDefaultSuccessHandler(ContainerBuilder $container, array $config): Definition
    {
        if($container->hasDefinition($serviceId = 'authentication.handler.success.default')) {
            return $container->getDefinition($serviceId);
        }

        return $container->addDefinition($serviceId)
            ->setType(DefaultSuccessfulAuthenticationHandler::class)
            ->setArguments([
                $container->getDefinition('authentication.utilities.web'),
                $config
            ]);
    }

    protected function createFailedAuthenticationHandler(
        ContainerBuilder $container,
        string $id,
        \stdClass $config
    ): Definition
    {
        $id = $this->getFailureHandlerId($id);
        $config = get_object_vars($config);
        $options = array_intersect_key($config, $this->defaultFailureHandlerOptions);

        if(isset($config['failure_handler'])) {
            if($config['failure_handler'] === 'default') {
                $this->createDefaultFailureHandler($container, $config);
            }

            return $container->addDefinition($id)
                ->setType(CustomFailedAuthenticationHandler::class)
                ->setArguments([
                    $container->getDefinition('authentication.handler.failure.' . $config['failure_handler']),
                    $options
                ]);
        }

        return $this->createDefaultFailureHandler($container, $options);
    }

    private function createDefaultFailureHandler(ContainerBuilder $container, array $options): Definition
    {
        if($container->hasDefinition($serviceId = 'authentication.handler.failure.default')) {
            return $container->getDefinition($serviceId);
        }

        return $container->addDefinition($serviceId)
            ->setType(DefaultFailedAuthenticationHandler::class)
            ->setArguments([
                $container->getDefinition('kernel'),
                $container->getDefinition('authentication.utilities.web'),
                $options,
                $container->getDefinition('logger')
            ])
            ->addTag('logger', 'security.authentication.handler.failure');
    }

    protected function getSuccessHandlerId(string $id)
    {
        return 'authentication.handler.success.' . $id . '.'.str_replace('-', '_', $this->getKey());
    }

    protected function getFailureHandlerId(string $id)
    {
        return 'authentication.handler.failure.' . $id . '.'.str_replace('-', '_', $this->getKey());
    }

    protected function createEntryPoint(
        ContainerBuilder $container,
        string $id,
        \stdClass $config,
        ?string $defaultEntryPointId
    ): ?string
    {
        return $defaultEntryPointId;
    }

    protected function isRememberMeAware(array $config): bool
    {
        return $config['remember_me'];
    }

}