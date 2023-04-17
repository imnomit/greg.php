<?php

namespace nomit\Kernel\DependencyInjection;

use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\Definitions\Statement;
use nomit\Schema\Expect;
use nomit\Template\Bridges\Kernel\KernelTemplateFactory;
use nomit\Template\Bridges\Kernel\TemplateFactory;
use nomit\Template\Engine;

final class TemplateExtension extends CompilerExtension
{

    public function __construct(
        private string $temporaryDirectory,
        private bool $debugMode
    )
    {
    }

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'debugger' => Expect::anyOf(true, false, 'all'),
            'macros' => Expect::arrayOf('string'),
            'extensions' => Expect::arrayOf('string|nomit\DependencyInjection\Definitions\Statement'),
            'templateClass' => Expect::string(),
            'strictTypes' => Expect::bool(false),
        ]);
    }

    public function loadConfiguration()
    {
        if (!class_exists(Engine::class)) {
            return;
        }

        $config = $this->config;
        $builder = $this->getContainerBuilder();

        $builder->addFactoryDefinition($this->prefix('kernelTemplateFactory'))
            ->setImplement(KernelTemplateFactory::class)
            ->getResultDefinition()
            ->setFactory(Engine::class)
            ->addSetup('setTempDirectory', [$this->temporaryDirectory])
            ->addSetup('setAutoRefresh', [$this->debugMode])
            ->addSetup('setStrictTypes', [$config->strictTypes]);

        if (version_compare(Engine::VERSION, '3', '<')) {
            foreach ($config->macros as $macro) {
                $this->addMacro($macro);
            }
        } else {
            foreach ($config->extensions as $extension) {
                $this->addExtension($extension);
            }
        }

        $builder->addDefinition($this->prefix('templateFactory'))
            ->setFactory(TemplateFactory::class)
            ->setArguments(['templateClass' => $config->templateClass, 'cache' => $builder->getDefinition('cache')])
            ->addTag('cache', 'application');

        if ($this->name === 'template') {
            $builder->addAlias('nomit.kernelTemplateFactory', $this->prefix('kernelTemplateFactory'));
            $builder->addAlias('nomit.templateFactory', $this->prefix('templateFactory'));
        }
    }

    public function addMacro(string $macro): void
    {
        $builder = $this->getContainerBuilder();
        $definition = $builder->getDefinition($this->prefix('latteFactory'))->getResultDefinition();

        if (($macro[0] ?? null) === '@') {
            if (str_contains($macro, '::')) {
                [$macro, $method] = explode('::', $macro);
            } else {
                $method = 'install';
            }

            $definition->addSetup('?->onCompile[] = function ($engine) { ?->' . $method . '($engine->getCompiler()); }', ['@self', $macro]);

        } else {
            if (!str_contains($macro, '::') && class_exists($macro)) {
                $macro .= '::install';
            }

            $definition->addSetup('?->onCompile[] = function ($engine) { ' . $macro . '($engine->getCompiler()); }', ['@self']);
        }
    }


    /** @param Statement|string $extension */
    public function addExtension($extension): void
    {
        $extension = is_string($extension)
            ? new Statement($extension)
            : $extension;

        $builder = $this->getContainerBuilder();
        $builder->getDefinition($this->prefix('templateFactory'))
            ->getResultDefinition()
            ->addSetup('addExtension', [$extension]);
    }

}