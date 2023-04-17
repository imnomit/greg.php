<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Generator;


use nomit\Exception\InvalidStateException;
use nomit\Utility\Object\SmartObjectTrait;

/**
 * Class property description.
 *
 * @property-deprecated mixed $value
 */
final class Property
{
	use SmartObjectTrait;
	use Traits\NameAware;
	use Traits\VisibilityAware;
	use Traits\CommentAware;
	use Traits\AttributeAware;

	private mixed $value = null;
	private bool $static = false;
	private ?string $type = null;
	private bool $nullable = false;
	private bool $initialized = false;
	private bool $readOnly = false;


	public function setValue(mixed $val): static
	{
		$this->value = $val;
		$this->initialized = true;
		return $this;
	}


	public function &getValue(): mixed
	{
		return $this->value;
	}


	public function setStatic(bool $state = true): static
	{
		$this->static = $state;
		return $this;
	}


	public function isStatic(): bool
	{
		return $this->static;
	}


	public function setType(?string $type): static
	{
		$this->type = Utilities::validateType($type, $this->nullable);
		return $this;
	}


	public function getType(bool $asObject = false): Type|string|null
	{
		return $asObject && $this->type
			? \nomit\Utility\Type::fromString($this->type)
			: $this->type;
	}


	public function setNullable(bool $state = true): static
	{
		$this->nullable = $state;
		return $this;
	}


	public function isNullable(): bool
	{
		return $this->nullable;
	}


	public function setInitialized(bool $state = true): static
	{
		$this->initialized = $state;
		return $this;
	}


	public function isInitialized(): bool
	{
		return $this->initialized || $this->value !== null;
	}


	public function setReadOnly(bool $state = true): static
	{
		$this->readOnly = $state;
		return $this;
	}


	public function isReadOnly(): bool
	{
		return $this->readOnly;
	}


	/** @throws InvalidStateException */
	public function validate(): void
	{
		if ($this->readOnly && !$this->type) {
			throw new InvalidStateException("Property \$$this->name: Read-only properties are only supported on typed property.");
		}
	}
}
