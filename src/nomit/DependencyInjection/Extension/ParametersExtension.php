<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\DependencyInjection\Extension;

use nomit\DependencyInjection\DynamicParameter;
use nomit\DependencyInjection\Utilities;
use nomit\Dumper\Dumper;
use nomit\Kernel\Secret\SecretFactory;


/**
 * Parameters.
 */
final class ParametersExtension extends \nomit\DependencyInjection\CompilerExtension
{
	/** @var string[] */
	public array $dynamicParams = [];

	/** @var string[][] */
	public array $dynamicValidators = [];
	private array $compilerConfig;


	public function __construct(array &$compilerConfig)
	{
		$this->compilerConfig = &$compilerConfig;
	}


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$resolver = new \nomit\DependencyInjection\Resolver($builder);
		$generator = new \nomit\DependencyInjection\PhpGenerator($builder);

        foreach($builder->findByTag('kernel.secret.loader') as $serviceName => $tagValue) {
            $this->initialization->addBody('$this->parameters[\'secret\'] = array_merge($this->getService(?)->list(), $this->parameters[\'secret\'] ?? []);', [$serviceName]);
        }

        $builder->addDefinition($this->prefix('factory'))
            ->setType(SecretFactory::class);

        $builder->addAlias('secret', $this->prefix('factory'));

        $params = $this->config;
        $params = $resolver->completeArguments($params);

        foreach ($this->dynamicParams as $key) {
			$params[$key] = array_key_exists($key, $params)
				? new DynamicParameter($generator->formatPhp('($this->parameters[?] \?\? ?)', $resolver->completeArguments(Utilities::filterArguments([$key, $params[$key]]))))
				: new DynamicParameter((new \nomit\Generator\Dumper)->format('$this->parameters[?]', $key));
		}

		$builder->parameters = \nomit\DependencyInjection\Utilities::expand($params, $params, true);

		// expand all except 'services'
		$slice = array_diff_key($this->compilerConfig, ['services' => 1]);
		$slice = \nomit\DependencyInjection\Utilities::expand($slice, $builder->parameters);
		$this->compilerConfig = $slice + $this->compilerConfig;
	}

	public function beforeCompile()
    {
        $container = $this->getContainerBuilder();
        $definition = $container->getDefinition($this->prefix('factory'));
        $vaults = [];

        foreach($container->findByTag('kernel.secret.loader') as $serviceName => $tagValue) {
            $vaults[] = $container->getDefinition($serviceName);
        }

        $definition->setArguments([
            $vaults
        ]);
    }

    public function afterCompile(\nomit\Generator\ClassType $class)
	{
		$parameters = $this->getContainerBuilder()->parameters;

		array_walk_recursive($parameters, function (&$val): void {
			if ($val instanceof \nomit\DependencyInjection\Definitions\Statement || $val instanceof DynamicParameter) {
				$val = null;
			}
		});

		$cnstr = $class->getMethod('__construct');
		$cnstr->addBody('$this->parameters += ?;', [$parameters]);

		foreach ($this->dynamicValidators as [$param, $expected]) {
			if ($param instanceof \nomit\DependencyInjection\Definitions\Statement) {
				continue;
			}

			$cnstr->addBody('nomit\Utilities\Validators::assert(?, ?, ?);', [$param, $expected, 'dynamic parameter']);
		}
	}
}
