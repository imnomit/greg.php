<?php

namespace nomit\Notification\DependencyInjection;

use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\ContainerBuilder;
use nomit\DependencyInjection\Definitions\Definition;
use nomit\DependencyInjection\Exception\InvalidConfigurationException;
use nomit\DependencyInjection\Exception\MissingServiceException;
use nomit\Dumper\Dumper;
use nomit\Notification\Notifier;
use nomit\Notification\Response\Responder;
use nomit\Notification\Serialization\Serializer;
use nomit\Notification\Storage\BagStorage;
use nomit\Notification\Storage\StorageManager;
use nomit\Schema\Expect;

final class NotificationExtension extends CompilerExtension
{

    private ?Definition $storageManager = null;

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'path' => Expect::string('/api/notifications'),
            'bag' => Expect::string('filesystem'),
            'default_format' => Expect::string('json'),
            'serialization' => Expect::structure([
                'format' => Expect::string(),
                'context' => Expect::array()
            ]),
            'views' => Expect::arrayOf(Expect::structure([
                'service' => Expect::string(),
                'serialized' => Expect::structure([
                    'format' => Expect::string()
                ])
            ])),
            'criteria' => Expect::structure([
                'ttl' => Expect::int(86400)
            ]),
            'context' => Expect::array(),
            'maximum_duration' => Expect::int(10000),
            'lock' => Expect::structure([
                'prefix' => Expect::string()
            ])
        ]);
    }

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();

        $this->loadDefinitionsFromConfig(
            $this->loadFromFile(
                dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.neon'
            )
        );

        $this->createNotifier($container);
    }

    private function createNotifier(ContainerBuilder $container): void
    {
        $container->addDefinition($this->prefix('notifier'))
            ->setType(Notifier::class);

        $container->addAlias('notifier', $this->prefix('notifier'));
    }

    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();
        $config = $this->config;

        $this->createNotifyingEventListener($container, $config);
        $this->registerStoreNotificationsEventListener($container, $config);
        $this->registerCommands($container, $config);
    }

    private function createNotifyingEventListener(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->getDefinition($this->prefix('event_listener.notifying'))
            ->setArguments([
                $this->registerNotifier($container, $config),
                $container->getDefinition('authentication.token.storage'),
                $config,
                $container->getDefinition('logger')
            ])
            ->addTag('logger', 'notification');
    }

    private function registerNotifier(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->getDefinition($this->prefix('notifier'))
            ->setArguments([
                $config->default_format,
                $this->createResponder($container, $config),
                $this->createStorageManager($container, $config),
            ]);
    }

    private function createResponder(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->addDefinition($this->prefix('response.responder'))
            ->setType(Responder::class)
            ->setArguments([
                $this->createStorageManager($container, $config),
                $container->getDefinition('event_dispatcher'),
                $this->createViews($container, $config->views)
            ]);
    }

    private function createStorageManager(ContainerBuilder $container, \stdClass $config): Definition
    {
        if($this->storageManager) {
            return $this->storageManager;
        }

        return $this->storageManager = $container->getDefinition($this->prefix('storage.manager'))
            ->setArguments([
                $this->createBagStorage($container, $config),
                $container->getDefinition('event_dispatcher'),
                $container->getDefinition('logger'),
                get_object_vars($config->criteria),
            ])
            ->addTag('logger', 'notification.storage');
    }

    private function createBagStorage(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->addDefinition($this->prefix('storage.bag'))
            ->setType(BagStorage::class)
            ->setArguments([
                $this->createStorageBag($container, $config->bag, $config),
                $container->getDefinition('event_dispatcher'),
                $config->criteria
            ]);
    }

    private function createStorageBag(ContainerBuilder $container, string $serviceName, \stdClass $config): Definition
    {
        $serviceId = $this->prefix('storage.bag.' . $serviceName);

        if(!$container->hasDefinition($serviceId)) {
            throw new InvalidConfigurationException(sprintf('The configured notification storage bag, named "%s", references a non-existent service.', $serviceName));
        }

        $definition = $container->getDefinition($serviceId);

        switch($serviceName) {
            case 'filesystem':
                return $definition->setArguments([
                    $container->getDefinition('filesystem'),
                    $this->createSerializer($container, $config->serialization),
                    $container->getDefinition('lock.factory'),
                    $container->parameters['paths']['tmp'],
                    $config->lock->prefix,
                    $container->getDefinition('logger')
                ])
                ->addTag('logger', 'notification.storage.bag');
        }

        return $definition;
    }

    private function createSerializer(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->addDefinition($this->prefix('serialization.serializer'))
            ->setType(Serializer::class)
            ->setArguments([
                $container->getDefinition('serializer'),
                $config->format,
                $config->context
            ]);
    }

    private function createViews(ContainerBuilder $container, array $views): array
    {
        $viewServices = [];

        foreach($views as $format => $viewConfig) {
            $viewServices[$format] = $this->createView($container, $format, $viewConfig->service, $viewConfig);
        }

        return $viewServices;
    }

    private function createView(ContainerBuilder $container, string $format, string $serviceName, \stdClass $config): Definition
    {
        $serviceId = $this->prefix('response.view.' . $serviceName);

        if(!$container->hasDefinition($serviceId)) {
            throw new InvalidConfigurationException(sprintf('The configured response view, for the format "%s" and with the service name "%s",references a non-existent service.', $format, $serviceName));
        }

        $definition = $container->getDefinition($serviceId);

        switch($serviceName) {
            case 'serialized':
                $definition->setArgument(1, $config->serialized->format);
        }

        return $definition;
    }

    private function registerStoreNotificationsEventListener(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->getDefinition($this->prefix('event_listener.store_notifications'))
            ->setArguments([
                $this->createStorageManager($container, $config),
                $container->getDefinition('logger')
            ])
            ->addTag('logger', 'notification.event_listener');
    }

    private function registerCommands(ContainerBuilder $container, \stdClass $config): void
    {
        foreach($container->findByTag('notification.console.command') as $serviceId => $tagValue) {
            if(!$container->hasDefinition($serviceId)) {
                throw new MissingServiceException(sprintf('The current container is missing the notification command service with the service ID "%s".', $serviceId));
            }

            $userProviders = array_map(function($userProvider) use($container) {
                if(!$container->hasDefinition($userProvider)) {
                    throw new MissingServiceException(sprintf('The current container is missing the user provider service with the service ID "%s".', $userProvider));
                }

                return $container->getDefinition($userProvider);
            }, $container->parameters['user_providers']);

            $container->getDefinition($serviceId)
                ->setArguments([
                    $container->getDefinition($this->prefix('notifier')),
                    $userProviders
                ]);
        }
    }

}