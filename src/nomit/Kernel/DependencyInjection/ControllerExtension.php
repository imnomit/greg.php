<?php

namespace nomit\Kernel\DependencyInjection;

use nomit\ClassLoader\ClassLoader;
use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\Container;
use nomit\DependencyInjection\ContainerBuilder;
use nomit\DependencyInjection\Definitions\Definition;
use nomit\DependencyInjection\Definitions\ServiceDefinition;
use nomit\DependencyInjection\Definitions\Statement;
use nomit\DependencyInjection\Extension\InjectExtension;
use nomit\Dumper\Dumper;
use nomit\Exception\NotSupportedException;
use nomit\Kernel\Component\ControllerFactory;
use nomit\Kernel\Component\ControllerFactoryCallback;
use nomit\Kernel\Component\ControllerFactoryInterface;
use nomit\Kernel\Component\ControllerInterface;
use nomit\Kernel\EventListener\ControllerEventListener;
use nomit\Schema\Expect;
use nomit\Utility\FileSystem;

final class ControllerExtension extends CompilerExtension
{

    private ?string $touch = null;

    public function __construct(
        private bool $debugMode = false,
        private array $scanDirectories = [],
        private ?string $temporaryDirectory = null,
        private ?ClassLoader $loader = null
    )
    {
    }

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'mapping' => Expect::arrayOf('string|array'),
            'scanDirectories' => Expect::anyOf(
                Expect::arrayOf('string')->default($this->scanDirectories)->mergeDefaults(),
                false,
            )->firstIsDefault(),
            'scanFilter' => Expect::string('*Controller'),
        ]);
    }

    public function loadConfiguration()
    {
        $config = $this->config;
        $container = $this->getContainerBuilder();

        if ($this->debugMode && ($config->scanDirectories || $this->loader) && $this->temporaryDirectory) {
            $this->touch = $this->temporaryDirectory . '/touch';

            FileSystem::createDirectory($this->temporaryDirectory);

            $this->getContainerBuilder()->addDependency($this->touch);
        }

        $this->createControllerFactory($container, $config, $this->touch);
        $this->createControllerEventListener($container);

        $this->registerControllers($container, $config);
    }

    private function createControllerFactory(ContainerBuilder $container, \stdClass $config, string $touch = null): Definition
    {
        $factory = $container->addDefinition($this->prefix('factory'))
            ->setType(ControllerFactoryInterface::class)
            ->setFactory(ControllerFactory::class, [
                $container->getDefinitionByType(Container::class),
                new Statement(
                    ControllerFactoryCallback::class,
                    [
                        1 => $touch ?? null
                    ]
                )
            ]);

        if($config->mapping) {
            $factory->addSetup('setMapping', [$config->mapping]);
        }

        return $factory;
    }

    private function createControllerEventListener(ContainerBuilder $container): void
    {
        $container->addDefinition($this->prefix('event_listener.controller'))
            ->setType(ControllerEventListener::class)
            ->addTag('event_subscriber');
    }

    public function beforeCompile()
    {
        $config = $this->config;
        $container = $this->getContainerBuilder();

        $this->registerControllerEventListener($container);
    }

    private function registerControllers(ContainerBuilder $container, \stdClass $config): void
    {
        $all = [];

        foreach ($container->findByType(ControllerInterface::class) as $def) {
            $all[$def->getType()] = $def;
        }

        $counter = 0;

        foreach ($this->findControllers($config) as $class) {
            if (empty($all[$class])) {
                $all[$class] = $container->addDefinition($this->prefix((string) ++$counter))
                    ->setType($class);
            }
        }

        foreach ($all as $def) {
            $def->addTag('logger', 'application.controller');
            $def->addTag('cache', 'application.controller');

            $def->addTag(InjectExtension::TAG_INJECT)
                ->setAutowired(false);

            $def->addSetup('setEventDispatcher', [
                $container->getDefinition('event_dispatcher')
            ]);

            $this->compiler->addExportedType($def->getType());
        }
    }

    private function findControllers(\stdClass $config): array
    {
        if ($config->scanDirectories) {
            if (!class_exists(ClassLoader::class)) {
                throw new NotSupportedException("ClassLoader is required to find controllers, install package `nette/robot-loader` or disable option {$this->prefix('scanDirectories')}: false");
            }

            $robot = new ClassLoader();
            $robot->addDirectory(...$config->scanDirectories);
            $robot->acceptFiles = [$config->scanFilter . '.php'];

            if ($this->temporaryDirectory) {
                $robot->setTemporaryDirectory($this->temporaryDirectory);
                $robot->refresh();
            } else {
                $robot->rebuild();
            }
        } elseif ($this->loader && $config->scanDirectories !== false) {
            $robot = $this->loader;

            $robot->refresh();
        }

        $classes = [];

        if (isset($robot)) {
            $classes = array_keys($robot->getIndexedClasses());
        }

        $controllers = [];

        foreach (array_unique($classes) as $class) {
            if (
                fnmatch($config->scanFilter, $class)
                && class_exists($class)
                && ($rc = new \ReflectionClass($class))
                && $rc->implementsInterface(ControllerInterface::class)
                && !$rc->isAbstract()
            ) {
                $controllers[] = $rc->getName();
            }
        }

        return $controllers;
    }

    private function registerControllerEventListener(ContainerBuilder $container): void
    {
        $container->getDefinition($this->prefix('event_listener.controller'))
            ->setArguments([
                $container->parameters['user_providers']
            ]);
    }

}