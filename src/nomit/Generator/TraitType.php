<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Generator;


use nomit\Exception\InvalidStateException;

/**
 * Trait description.
 *
 * @property-deprecated Method[] $methods
 * @property-deprecated Property[] $properties
 */
final class TraitType extends ClassLike
{
	use Traits\MethodsAware;
	use Traits\PropertiesAware;
	use Traits\TraitsAware;

	public function addMember(Method|Property|Constant|TraitUse $member): static
	{
		$name = $member->getName();
		[$type, $n] = match (true) {
			$member instanceof Method => ['methods', strtolower($name)],
			$member instanceof Property => ['properties', $name],
			$member instanceof TraitUse => ['traits', $name],
		};
		if (isset($this->$type[$n])) {
			throw new InvalidStateException("Cannot add member '$name', because it already exists.");
		}
		$this->$type[$n] = $member;
		return $this;
	}


	public function __clone()
	{
		$clone = fn($item) => clone $item;
		$this->methods = array_map($clone, $this->methods);
		$this->properties = array_map($clone, $this->properties);
		$this->traits = array_map($clone, $this->traits);
	}
}
