<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Cache;


use nomit\Exception\InvalidStateException;
use nomit\Utility\Object\SmartObjectTrait;

/**
 * Output caching helper.
 */
class OutputHelper
{
	use SmartObjectTrait;

	public array $dependencies = [];

	private ?Cache $cache;

	private mixed $key;


	public function __construct(Cache $cache, mixed $key)
	{
		$this->cache = $cache;
		$this->key = $key;
		ob_start();
	}


	/**
	 * Stops and saves the cache.
	 */
	public function end(array $dependencies = []): void
	{
		if ($this->cache === null) {
			throw new InvalidStateException('Output cache has already been saved.');
		}

		$this->cache->save($this->key, ob_get_flush(), $dependencies + $this->dependencies);
		$this->cache = null;
	}


	/**
	 * Stops and throws away the output.
	 */
	public function rollback(): void
	{
		ob_end_flush();
		$this->cache = null;
	}
}
