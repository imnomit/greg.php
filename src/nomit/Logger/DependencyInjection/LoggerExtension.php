<?php

namespace nomit\Logger\DependencyInjection;

use nomit\DependencyInjection\Definitions\Definition;
use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\ContainerBuilder;
use nomit\DependencyInjection\Exception\InvalidConfigurationException;
use nomit\DependencyInjection\Exception\MissingServiceException;
use nomit\Dumper\Dumper;
use nomit\Logger\Handler\StreamHandler;
use nomit\Logger\Logger;
use nomit\Logger\Processor\GitProcessor;
use nomit\Logger\Processor\HostnameProcessor;
use nomit\Logger\Processor\IntrospectionProcessor;
use nomit\Logger\Processor\MemoryPeakUsageProcessor;
use nomit\Logger\Processor\MemoryUsageProcessor;
use nomit\Logger\Processor\MercurialProcessor;
use nomit\Logger\Processor\ProcessIdProcessor;
use nomit\Logger\Processor\PsrLogMessageProcessor;
use nomit\Logger\Processor\TagProcessor;
use nomit\Logger\Processor\UidProcessor;
use nomit\Logger\Processor\WebProcessor;
use nomit\Schema\Expect;
use Psr\Log\LoggerInterface;

class LoggerExtension extends CompilerExtension
{

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::arrayOf(
            Expect::structure([
                'level' => Expect::int(100),
                'bubble' => Expect::bool(false),
                'stream' => Expect::structure([
                    'pathname' => Expect::string(),
                    'ttl' => Expect::int(86400)
                ]),
                'handlers' => Expect::arrayOf(
                    Expect::string()
                ),
                'processors' => Expect::arrayOf(
                    Expect::string()
                ),
            ])
        )->before(fn($val) => is_array(reset($val)) || reset($val) === null
            ? $val
            : ['default' => $val]);
    }

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();

        $this->loadDefinitionsFromConfig(
            $this->loadFromFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.neon')
        );

        $this->createLoggers($container);
    }

    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();

        $this->createLoggerChannels($container);
        $this->registerCollectGarbageCommand($container);
    }

    private function createLoggers(ContainerBuilder $container): void
    {
        foreach($this->config as $name => $config) {
            $this->createLogger($container, $name, $config);
        }
    }

    private function createLogger(ContainerBuilder $container, string $name, \stdClass $config): void
    {
        $container->addDefinition($this->prefix($name))
            ->setFactory(Logger::class, [
                $name,
                $this->createHandlers($container, $name, $config->handlers),
                $this->createProcessors($container, $name, $config->processors)
            ]);

        if(!$container->hasDefinition('logger')) {
            $container->addAlias('logger', $this->prefix($name));
        }
    }

    private function createHandlers(ContainerBuilder $container, string $name, array $handlers): array
    {
        $handlerServices = [];

        foreach($handlers as $index => $handlerName) {
            foreach($container->findByTag('logger.handler') as $serviceName => $tagValue) {
                if($serviceName === $this->prefix('handler.' . $handlerName)) {
                    if($handlerName === 'stream') {
                        $this->createStreamHandler($container, $name, $this->config[$name]);
                    }

                    $handlerServices[] = $container->getDefinition($this->prefix('handler.' . $handlerName . '.' . $name));
                }
            }
        }

        return $handlerServices;
    }

    private function createStreamHandler(ContainerBuilder $container, string $name, \stdClass $config): void
    {
        $serviceId = $this->prefix('handler.stream.' . $name);

        if($container->hasDefinition($serviceId)) {
            return;
        }

        $container->addDefinition($serviceId)
            ->setType(StreamHandler::class)
            ->setArguments([
                $config->stream->pathname . DIRECTORY_SEPARATOR . $name . '.log',
                $config->level,
                $config->bubble,
            ])
            ->addTag('logger.handler');
    }

    private function createProcessors(ContainerBuilder $container, string $name, array $processors): array
    {
        $processorServices = [];

        foreach($processors as $processorName) {
            foreach($container->findByTag('logger.processor') as $serviceName => $tagValue) {
                if($serviceName === $this->prefix('processor.' . $processorName)) {
                    $serviceId = $this->createProcessor($container, $name, $processorName, $this->config[$name]);

                    $processorServices[] = $container->getDefinition($serviceId);
                }
            }
        }

        return $processorServices;
    }

    private function createProcessor(ContainerBuilder $container, string $name, string $processorName, \stdClass $config): string
    {
        $usesLevel = false;

        switch($processorName) {
            case 'git':
                $className = GitProcessor::class;
                $usesLevel = true;
                break;

            case 'hostname':
                $className = HostnameProcessor::class;
                break;

            case 'introspection':
                $className = IntrospectionProcessor::class;
                $usesLevel = true;
                break;

            case 'memory.peak_usage':
                $className = MemoryPeakUsageProcessor::class;
                break;

            case 'memory.memory_usage':
                $className = MemoryUsageProcessor::class;
                break;

            case 'mercurial':
                $className = MercurialProcessor::class;
                $usesLevel = true;
                break;

            case 'process_id':
                $className = ProcessIdProcessor::class;
                break;

            case 'psr_log_message':
                $className = PsrLogMessageProcessor::class;
                break;

            case 'tag':
                $className = TagProcessor::class;
                break;

            case 'uid':
                $className = UidProcessor::class;
                break;

            case 'web':
                $className = WebProcessor::class;
                break;

            default:
                throw new InvalidConfigurationException(sprintf('The supplied logger processor name, "%s", is not supported.', $processorName));
        }

        $processId = $this->prefix('processor.' . $processorName . '.' . $name);

        if($container->hasDefinition($processId)) {
            return $processId;
        }

        $definition = $container->addDefinition($processId)
            ->setType($className);

        if($usesLevel) {
            $definition->setArguments([
                $config->level
            ]);
        }

        $definition->addTag('logger.processor');

        return $processId;
    }

    private function createLoggerChannels(ContainerBuilder $container): void
    {
        foreach($container->findByTag('logger') as $serviceName => $channel) {
            if(is_array($channel)) {
                $channel = $channel['channel'];
            }

            $definition = $container->getDefinition($serviceName);
            $reflection = new \ReflectionClass($definition->getType());
            $constructor = $reflection->getConstructor();

            if($constructor === null) {
                continue;
            }

            foreach($constructor->getParameters() as $index => $parameter) {
                if($parameter->getName() === 'logger' || $parameter->getType() === LoggerInterface::class) {
                    $definition->setArgument($index, $this->createLoggerChannel($container, $channel));
                }
            }
        }
    }

    private function createLoggerChannel(ContainerBuilder $container, string $channelName): Definition
    {
        $loggerNames = array_keys($this->config);
        $defaultName = reset($loggerNames);
        $channelId = $this->prefix('logger.' . $defaultName . '.' . $channelName);
        $config = $this->config[$defaultName];

        if($container->hasDefinition($channelId)) {
            return $container->getDefinition($channelId);
        }

        return $container->addDefinition($channelId)
            ->setFactory(Logger::class, [
                $channelName,
                $this->createHandlers($container, $defaultName, $config->handlers),
                $this->createProcessors($container, $defaultName, $config->processors)
            ])
            ->addTag('logger.channel', $channelName);
    }

    private function registerCollectGarbageCommand(ContainerBuilder $container): void
    {
        if(!$container->hasDefinition($serviceId = $this->prefix('command.collect_garbage'))) {
            throw new MissingServiceException(sprintf('The logger garbage collection command service with the service ID "%s" is missing from the current container.', $serviceId));
        }

        $container->getDefinition($serviceId)
            ->setArguments([
                $container->getDefinition('filesystem'),
                $container->parameters['paths']['tmp'] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR,
                $container->getDefinition('lock.factory'),
            ]);
    }

}