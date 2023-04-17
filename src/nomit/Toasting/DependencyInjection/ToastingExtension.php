<?php

namespace nomit\Toasting\DependencyInjection;

use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\ContainerBuilder;
use nomit\DependencyInjection\Definitions\Definition;
use nomit\DependencyInjection\Exception\InvalidConfigurationException;
use nomit\Schema\Expect;
use nomit\Toasting\Response\Responder;
use nomit\Toasting\Storage\BagStorage;
use nomit\Toasting\Storage\StorageManager;
use nomit\Toasting\Toaster;
use nomit\Toasting\ToasterInterface;

final class ToastingExtension extends CompilerExtension
{

    private ?Definition $storageManager = null;

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'path' => Expect::string('/api/toasts'),
            'bag' => Expect::string('session'),
            'default_format' => Expect::string('json'),
            'views' => Expect::arrayOf(Expect::structure([
                'service' => Expect::string(),
                'serialized' => Expect::structure([
                    'format' => Expect::string()
                ])
            ])),
            'criteria' => Expect::array(),
            'context' => Expect::array(),
            'maximum_duration' => Expect::int(10000),
        ]);
    }

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();

        $this->loadDefinitionsFromConfig(
            $this->loadFromFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.neon')
        );

        $this->createToaster($container);
    }

    private function createToaster(ContainerBuilder $container): void
    {
        $container->addDefinition($this->prefix('toaster'))
            ->setType(Toaster::class);

        $container->addAlias('toaster', $this->prefix('toaster'));
    }

    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();
        $config = $this->config;

        $this->createToastingEventListener($container, $config);
    }

    private function createToastingEventListener(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->getDefinition($this->prefix('event_listener.toasting'))
            ->setArguments([
                $this->registerToaster($container, $config),
                $config,
                $container->getDefinition('logger')
            ])
            ->addTag('logger', 'toasting');
    }

    private function registerToaster(ContainerBuilder $container, \stdClass $config): Definition
    {
        $definition = $container->getDefinition($this->prefix('toaster'))
            ->setArguments([
                $config->default_format,
                $this->createResponder($container, $config),
                $this->createStorageManager($container, $config)
            ]);

        return $definition;
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

        return $this->storageManager = $container->addDefinition($this->prefix('storage.manager'))
            ->setType(StorageManager::class)
            ->setArguments([
                $this->createBagStorage($container, $config),
                $container->getDefinition('event_dispatcher'),
                $config->criteria
            ]);
    }

    private function createBagStorage(ContainerBuilder $container, \stdClass $config): Definition
    {
        return $container->addDefinition($this->prefix('storage.bag'))
            ->setType(BagStorage::class)
            ->setArguments([
                $this->createStorage($container, $config->bag),
                $container->getDefinition('event_dispatcher'),
                $config->criteria
            ]);
    }

    private function createStorage(ContainerBuilder $container, string $serviceName): Definition
    {
        $serviceId = $this->prefix('storage.bag.' . $serviceName);

        if(!$container->hasDefinition($serviceId)) {
            throw new InvalidConfigurationException(sprintf('The configured toasting storage bag, named "%s", references a non-existent service.', $serviceName));
        }

        return $container->getDefinition($serviceId);
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

}