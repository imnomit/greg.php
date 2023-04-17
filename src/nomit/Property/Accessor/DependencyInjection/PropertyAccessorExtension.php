<?php

namespace nomit\Property\Accessor\DependencyInjection;

use nomit\DependencyInjection\CompilerExtension;
use nomit\Schema\Expect;
use nomit\Property\Accessor\PropertyAccessor;

class PropertyAccessorExtension extends CompilerExtension
{

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'magic_call' => Expect::bool(false),
            'magic_get' => Expect::bool(true),
            'magic_set' => Expect::bool(true),
            'throw_exception_on_invalid_index' => Expect::bool(false),
            'throw_exception_on_invalid_property_path' => Expect::bool(true)
        ]);
    }

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();

        $this->loadDefinitionsFromConfig(
            $this->loadFromFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.neon')['services']
        );

        $accessor = $container->getDefinition($this->prefix('accessor'));

        $magicMethods = PropertyAccessor::DISALLOW_MAGIC_METHODS;
        $magicMethods |= $this->config->magic_call ? PropertyAccessor::MAGIC_CALL : 0;
        $magicMethods |= $this->config->magic_get ? PropertyAccessor::MAGIC_GET : 0;
        $magicMethods |= $this->config->magic_set ? PropertyAccessor::MAGIC_SET : 0;

        $throw = PropertyAccessor::DO_NOT_THROW;
        $throw |= $this->config->throw_exception_on_invalid_index ? PropertyAccessor::THROW_ON_INVALID_INDEX : 0;
        $throw |= $this->config->throw_exception_on_invalid_property_path ? PropertyAccessor::THROW_ON_INVALID_PROPERTY_PATH : 0;

        $accessor->setArguments([
            $magicMethods,
            $throw
        ]);

        $container->addAlias('property_accessor', $this->prefix('accessor'));
    }

}