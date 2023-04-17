<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Component;


/**
 * Implementation of \ArrayAccess for IContainer.
 */
trait ArrayAccessTrait
{
	/**
	 * Adds the component to the container.
	 * @param  string|int  $name
	 * @param  ComponentInterface  $component
	 */
	public function offsetSet($name, $component): void
	{
		$name = is_int($name) ? (string) $name : $name;
		$this->addComponent($component, $name);
	}


	/**
	 * Returns component specified by name. Throws exception if component doesn't exist.
	 * @param  string|int  $name
	 */
	public function offsetGet($name): ComponentInterface
	{
		$name = is_int($name) ? (string) $name : $name;
		return $this->getComponent($name);
	}


	/**
	 * Does component specified by name exists?
	 * @param  string|int  $name
	 */
	public function offsetExists($name): bool
	{
		$name = is_int($name) ? (string) $name : $name;
		return $this->getComponent($name, false) !== null;
	}


	/**
	 * Removes component from the container.
	 * @param  string|int  $name
	 */
	public function offsetUnset($name): void
	{
		$name = is_int($name) ? (string) $name : $name;
		if ($component = $this->getComponent($name, false)) {
			$this->removeComponent($component);
		}
	}
}
