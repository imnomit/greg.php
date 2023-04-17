<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\DependencyInjection\Extension;


/**
 * Enables registration of other extensions in $config file
 */
class ExtensionsExtension extends \nomit\DependencyInjection\CompilerExtension
{
	public function getConfigSchema(): \nomit\Schema\Schema
	{
		return \nomit\Schema\Expect::arrayOf('string|nomit\DependencyInjection\Definitions\Statement');
	}


	public function loadConfiguration()
	{
		foreach ($this->getConfig() as $name => $class) {
			if (is_int($name)) {
				$name = null;
			}

			$args = [];
			if ($class instanceof \nomit\DependencyInjection\Definitions\Statement) {
				[$class, $args] = [$class->getEntity(), $class->arguments];
			}

			if (!is_a($class, \nomit\DependencyInjection\CompilerExtension::class, true)) {
				throw new \nomit\DependencyInjection\Exception\InvalidConfigurationException(sprintf(
					"Extension '%s' not found or is not CompilerExtension descendant.",
					$class,
				));
			}

			$this->compiler->addExtension($name, (new \ReflectionClass($class))->newInstanceArgs($args));
		}
	}
}
