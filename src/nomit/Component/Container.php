<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Component;


use nomit\Exception\InvalidArgumentException;
use nomit\Exception\InvalidStateException;
use nomit\Exception\UnexpectedValueException;
use nomit\Utility\Object\ObjectHelper;

/**
 * ComponentContainer is default implementation of IContainer.
 *
 * @property-read \Iterator $components
 */
class Container extends Component implements ContainerInterface
{
	private const NameRegexp = '#^[a-zA-Z0-9_]+$#D';

	/** @var ComponentInterface[] */
	private array $components = [];

	private ?Container $cloning = null;


	/********************* interface IContainer ****************d*g**/


	/**
	 * Adds the component to the container.
	 * @throws InvalidStateException
	 */
	public function addComponent(ComponentInterface $component, ?string $name, ?string $insertBefore = null): static
	{
		if ($name === null) {
			$name = $component->getName();
			if ($name === null) {
				throw new InvalidStateException("Missing component's name.");
			}
		}

		if (!preg_match(self::NameRegexp, $name)) {
			throw new InvalidArgumentException("Component name must be non-empty alphanumeric string, '$name' given.");
		}

		if (isset($this->components[$name])) {
			throw new InvalidStateException("Component with name '$name' already exists.");
		}

		// check circular reference
		$obj = $this;
		do {
			if ($obj === $component) {
				throw new InvalidStateException("Circular reference detected while adding component '$name'.");
			}

			$obj = $obj->getParent();
		} while ($obj !== null);

		// user checking
		$this->validateChildComponent($component);

		if (isset($this->components[$insertBefore])) {
			$tmp = [];
			foreach ($this->components as $k => $v) {
				if ((string) $k === $insertBefore) {
					$tmp[$name] = $component;
				}

				$tmp[$k] = $v;
			}

			$this->components = $tmp;
		} else {
			$this->components[$name] = $component;
		}

		try {
			$component->setParent($this, $name);
		} catch (\Throwable $e) {
			unset($this->components[$name]); // undo
			throw $e;
		}

		return $this;
	}


	/**
	 * Removes the component from the container.
	 */
	public function removeComponent(ComponentInterface $component): void
	{
		$name = $component->getName();
		if (($this->components[$name] ?? null) !== $component) {
			throw new InvalidArgumentException("Component named '$name' is not located in this container.");
		}

		unset($this->components[$name]);
		$component->setParent(null);
	}


	/**
	 * Returns component specified by name or path.
	 * @param  bool  $throw  throw exception if component doesn't exist?
	 */
	final public function getComponent(string $name, bool $throw = true): ?ComponentInterface
	{
		[$name] = $parts = explode(self::NameSeparator, $name, 2);

		if (!isset($this->components[$name])) {
			if (!preg_match(self::NameRegexp, $name)) {
				if ($throw) {
					throw new InvalidArgumentException("Component name must be non-empty alphanumeric string, '$name' given.");
				}

				return null;
			}

			$component = $this->createComponent($name);
			if ($component && !isset($this->components[$name])) {
				$this->addComponent($component, $name);
			}
		}

		$component = $this->components[$name] ?? null;
		if ($component !== null) {
			if (!isset($parts[1])) {
				return $component;

			} elseif ($component instanceof ContainerInterface) {
				return $component->getComponent($parts[1], $throw);

			} elseif ($throw) {
				throw new InvalidArgumentException("Component with name '$name' is not container and cannot have '$parts[1]' component.");
			}
		} elseif ($throw) {
			$hint = ObjectHelper::getSuggestion(array_merge(
				array_map('strval', array_keys($this->components)),
				array_map('lcfirst', preg_filter('#^createComponent([A-Z0-9].*)#', '$1', get_class_methods($this))),
			), $name);
			throw new InvalidArgumentException("Component with name '$name' does not exist" . ($hint ? ", did you mean '$hint'?" : '.'));
		}

		return null;
	}


	/**
	 * Component factory. Delegates the creation of components to a createComponent<Name> method.
	 */
	protected function createComponent(string $name): ?ComponentInterface
	{
		$ucname = ucfirst($name);
		$method = 'createComponent' . $ucname;
		if (
			$ucname !== $name
			&& method_exists($this, $method)
			&& (new \ReflectionMethod($this, $method))->getName() === $method
		) {
			$component = $this->$method($name);
			if (!$component instanceof ComponentInterface && !isset($this->components[$name])) {
				$class = static::class;
				throw new UnexpectedValueException("Method $class::$method() did not return or create the desired component.");
			}

			return $component;
		}

		return null;
	}


	/**
	 * Iterates over descendants components.
	 * @return \Iterator<int|string,ComponentInterface>
	 */
	final public function getComponents(bool $deep = false, ?string $filterType = null): \Iterator
	{
		$iterator = new RecursiveComponentIterator($this->components);
		if ($deep) {
			$iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
		}

		if ($filterType) {
			$iterator = new \CallbackFilterIterator($iterator, fn($item) => $item instanceof $filterType);
		}

		return $iterator;
	}


	/**
	 * Descendant can override this method to disallow insert a child by throwing an Nette\InvalidStateException.
	 * @throws InvalidStateException
	 */
	protected function validateChildComponent(ComponentInterface $child): void
	{
	}


	/********************* cloneable, serializable ****************d*g**/


	/**
	 * Object cloning.
	 */
	public function __clone()
	{
		if ($this->components) {
			$oldMyself = reset($this->components)->getParent();
			assert($oldMyself instanceof self);
			$oldMyself->cloning = $this;
			foreach ($this->components as $name => $component) {
				$this->components[$name] = clone $component;
			}

			$oldMyself->cloning = null;
		}

		parent::__clone();
	}


	/**
	 * Is container cloning now?
	 * @internal
	 */
	final public function _isCloning(): ?self
	{
		return $this->cloning;
	}
}
