<?php

namespace nomit\Console\DependencyInjection;

use nomit\Console\Command\CommandInterface;
use nomit\Console\Command\CommandRepository;
use nomit\Console\Command\DequeueCommand;
use nomit\Console\Command\EnqueueCommand;
use nomit\Console\Queue\PersistentQueue;
use nomit\Console\Serialization\Serializer;
use nomit\DependencyInjection\Definitions\Definition;
use nomit\Console\Exception\InvalidArgumentException;
use nomit\Console\Input\InputFactory;
use nomit\Console\Kernel;
use nomit\Console\Output\OutputFactory;
use nomit\Console\Queue\Persistence\FileSystemPersistence;
use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\Container;
use nomit\DependencyInjection\ContainerBuilder;
use nomit\DependencyInjection\Exception\InvalidConfigurationException;
use nomit\Dumper\Dumper;
use nomit\Schema\Expect;

class ConsoleExtension extends CompilerExtension
{

    protected array $map = [];

    public function __construct(
        private bool $debugMode
    )
    {
    }

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'name' => Expect::string('nomit'),
            'version' => Expect::string(),
            'commands' => Expect::arrayOf(
                Expect::structure([
                    'name' => Expect::string(),
                    'description' => Expect::string(),
                    'aliases' => Expect::arrayOf(Expect::string()),
                    'help' => Expect::string(),
                ])
            ),
            'reader' => Expect::structure([
                'service' => Expect::string(),
                'std' => Expect::structure([
                    'name' => Expect::string('std_reader')
                ])
            ]),
            'writer' => Expect::structure([
                'services' => Expect::arrayOf(Expect::string()),
                'file' => Expect::structure([
                    'resource' => Expect::string()
                ])
            ]),
            'providers' => Expect::arrayOf(Expect::string()),
            'queue' => Expect::structure([
                'queues' => Expect::arrayOf(Expect::string()),
                'serialization' => Expect::structure([
                    'format' => Expect::string('json')
                ]),
                'persistence' => Expect::structure([
                    'service' => Expect::string(),
                    'filesystem' => Expect::structure([
                        'directory' => Expect::string()
                    ])
                ])
            ])
        ]);
    }

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();

        $this->loadDefinitionsFromConfig(
            $this->loadFromFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.neon')
        );

        $this->createQueueCommands($container);
    }

    private function createQueueCommands(ContainerBuilder $container): void
    {
        $container->addDefinition($this->prefix('command.queue.enqueue'))
            ->setType(EnqueueCommand::class)
            ->addTag('console.command', [
                'command' => 'queue:enqueue'
            ]);

        $container->addDefinition($this->prefix('command.queue.dequeue'))
            ->setType(DequeueCommand::class)
            ->addTag('console.command', [
                'command' => 'queue:dequeue'
            ]);
    }

    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();
        $config = $this->config;

        $this->registerCommands($container);

        $this->createInputFactory($container, $this->prefix('input.reader.' . $this->config->reader->service));
        $this->createOutputFactory($container, $this->config->writer->services);

        $this->createKernel($container);

        $this->registerQueueCommands($container, $config->queue);
    }

    private function createKernel(ContainerBuilder $container): void
    {
        $definition = $container->addDefinition($this->prefix('kernel'))
            ->setFactory(Kernel::class, [
                '@' . Container::class,
            ]);

        if($this->config->name ?? null) {
            $definition->addSetup('setName', [$this->config->name]);
        }

        if($this->config->version ?? null) {
            $definition->addSetup('setVersion', [$this->config->version]);
        }

        if($container->hasDefinition('event_dispatcher')) {
            $definition->addSetup('setEventDispatcher', [$container->getDefinition('event_dispatcher')]);
        }

        if($this->config->providers ?? []) {
            foreach($this->config->providers as $providerId) {
                if(!$container->hasDefinition($providerId)) {
                    $providerId = $this->prefix('provider.' . $providerId);
                }

                if(!$container->hasDefinition($providerId)) {
                    throw new InvalidConfigurationException(sprintf('An invalid console provider service, "%s", has been referenced in the console configuration.', $providerId));
                }

                $definition->addSetup('addProvider', [$container->getDefinition($providerId)]);
            }
        }

        foreach($this->map as $name => $serviceId) {
            $service = $container->getDefinition($serviceId);
            $service->addSetup('setName', [$name]);

            $definition->addSetup('add', [$service]);
        }

        $container->addAlias('console', $this->prefix('kernel'));
    }

    private function registerCommands(ContainerBuilder $container): void
    {
        $commandServices = $container->findByTag('console.command');

        foreach($this->standardizeCommandNames($container, $this->config->commands ?? []) as $serviceId => $tagValue) {
            $commandServices[$serviceId] = $tagValue;
        }

        $lazyCommandMap = [];

        foreach($commandServices as $serviceId => $config) {
            $definition = $container->getDefinition($serviceId);
            $definition->setAutowired(false);

            $class = $definition->getType();

            if(isset($config['command'])) {
                $aliases = $config['command'];
            } else {
                $aliases = $class::getDefaultName();
            }

            $aliases = explode('|', $aliases ?? '');
            $commandName = array_shift($aliases);

            if ($isHidden = ('' === $commandName)) {
                $commandName = array_shift($aliases);
            }

            $description = $tags['description'] ?? null;

            if($aliases) {
                foreach($aliases as $alias) {
                    $lazyCommandMap[$alias] = $serviceId;
                }
            }

            $definition->addSetup('setName', [$commandName]);

            if($aliases) {
                $definition->addSetup('setAliases', [$aliases]);
            }

            if($isHidden) {
                $definition->addSetup('setHidden', [true]);
            }

            if($description) {
                $description = $class::getDefaultDescription();
            }

            if($description) {
                $definition->addSetup('setDescription', [$description]);
            }

            if(isset($config['help'])) {
                $definition->addSetup('setHelp', [$config['help']]);
            }

            $lazyCommandMap[$commandName] = $serviceId;
        }

        $container->addDefinition($this->prefix('command.repository'))
            ->setType(CommandRepository::class)
            ->setExported(true)
            ->setArguments([
                '@' . Container::class,
            ]);

        $this->map = $lazyCommandMap;
    }

    private function standardizeCommandNames(ContainerBuilder $container, array $commands): array
    {
        $standardized = [];

        foreach($commands as $name => $config) {
            $serviceId = $this->prefix($name);

            if(!$container->hasDefinition($serviceId)) {
                continue;
            }

            $standardized[$serviceId] = $config;
        }

        return $standardized;
    }

    private function createInputFactory(ContainerBuilder $container, string $readerId): void
    {
        $container->addDefinition($this->prefix('input.factory'))
            ->setFactory(InputFactory::class, [$container->getDefinition($readerId)]);
    }

    private function createOutputFactory(ContainerBuilder $container, array $writerIds): void
    {
        $container->addDefinition($this->prefix('output.factory'))
            ->setFactory(OutputFactory::class, [$this->getWriterServices($container, $writerIds)]);
    }

    private function getWriterServices(ContainerBuilder $container, array $writerIds): array
    {
        $writerServices = [];

        foreach($writerIds as $writerName) {
            $serviceId = $this->prefix('output.writer.' . $writerName);
            $definition = $container->getDefinition($serviceId);

            $writerServices[$serviceId] = $definition;
        }

        return $writerServices;
    }

    private function registerQueueCommands(ContainerBuilder $container, \stdClass $config): void
    {
        $commands = [
            $this->prefix('command.queue.enqueue'),
            $this->prefix('command.queue.dequeue')
        ];

        foreach($commands as $serviceId) {
            $container->getDefinition($serviceId)
                ->setArguments([
                    $this->createQueues($container, $config)
                ]);
        }
    }

    private function createQueues(ContainerBuilder $container, \stdClass $config): array
    {
        $queues = [];

        foreach($config->queues as $queueName) {
            $queues[] = $this->createQueue($container, $queueName, $config);
        }

        return $queues;
    }

    private function createQueue(ContainerBuilder $container, string $queueName, \stdClass $config): Definition
    {
        $persistenceServiceName = $config->persistence->service;
        $persistenceServiceConfig = $config->persistence->$persistenceServiceName ?? null;

        if(!$persistenceServiceConfig) {
            throw new InvalidConfigurationException(sprintf('Configuration is missing for the console queue persistence service named "%s".', $persistenceServiceName));
        }

        return $container->getDefinition($this->prefix('queue.' . $queueName))
            ->setArguments([
                $queueName,
                $this->createQueuePersistence($container, $queueName, $persistenceServiceName, $persistenceServiceConfig),
                $this->createQueueSerializer($container, $config->serialization->format)
            ]);
    }

    private function createQueueSerializer(ContainerBuilder $container, string $format): Definition
    {
        if($container->hasDefinition($this->prefix('queue.serializer'))) {
            return $container->getDefinition($this->prefix('queue.serializer'));
        }

        return $container->addDefinition($this->prefix('queue.serializer'))
            ->setType(Serializer::class)
            ->setArguments([
                $container->getDefinition('serializer'),
                $format
            ]);
    }

    private function createQueuePersistence(ContainerBuilder $container, string $queueName, string $serviceName, \stdClass $config): Definition
    {
        $serviceId = $this->prefix('queue.persistence.' . $serviceName . '.' . $queueName);

        return match($serviceName) {
            'filesystem' => $container->addDefinition($serviceId)
                ->setType(FileSystemPersistence::class)
                ->setArguments([
                    $container->getDefinition('filesystem'),
                    $config->directory,
                    $container->getDefinition('logger')
                ])
                ->addTag('logger', 'console.queue'),
            default => throw new InvalidConfigurationException(sprintf('The configured console queue persistence service, named "%s", is unsupported.', $serviceName))
        };
    }

}