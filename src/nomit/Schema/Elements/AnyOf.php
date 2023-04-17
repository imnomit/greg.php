<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Schema\Elements;

use nomit\Exception\InvalidStateException;
use nomit\Schema\Context;
use nomit\Schema\Utilities;
use nomit\Schema\Schema;
use nomit\Utility\Object\SmartObjectTrait;


final class AnyOf implements Schema
{
	use Base;
	use SmartObjectTrait;

	private array $set;


	public function __construct(mixed ...$set)
	{
		if (!$set) {
			throw new InvalidStateException('The enumeration must not be empty.');
		}

		$this->set = $set;
	}


	public function firstIsDefault(): self
	{
		$this->default = $this->set[0];
		return $this;
	}


	public function nullable(): self
	{
		$this->set[] = null;
		return $this;
	}


	public function dynamic(): self
	{
		$this->set[] = new Type(\nomit\Schema\DynamicParameter::class);
		return $this;
	}


	/********************* processing ****************d*g**/


	public function normalize($value, Context $context)
	{
		return $this->doNormalize($value, $context);
	}


	public function merge($value, $base)
	{
		if (is_array($value) && isset($value[Utilities::PREVENT_MERGING])) {
			unset($value[Utilities::PREVENT_MERGING]);
			return $value;
		}

		return Utilities::merge($value, $base);
	}


	public function complete($value, Context $context)
	{
		$expecteds = $innerErrors = [];
		foreach ($this->set as $item) {
			if ($item instanceof Schema) {
				$dolly = new Context;
				$dolly->path = $context->path;
				$res = $item->complete($item->normalize($value, $dolly), $dolly);
				if (!$dolly->errors) {
					$context->warnings = array_merge($context->warnings, $dolly->warnings);
					return $this->doFinalize($res, $context);
				}

				foreach ($dolly->errors as $error) {
					if ($error->path !== $context->path || empty($error->variables['expected'])) {
						$innerErrors[] = $error;
					} else {
						$expecteds[] = $error->variables['expected'];
					}
				}
			} else {
				if ($item === $value) {
					return $this->doFinalize($value, $context);
				}

				$expecteds[] = \nomit\Schema\Utilities::formatValue($item);
			}
		}

		if ($innerErrors) {
			$context->errors = array_merge($context->errors, $innerErrors);
		} else {
			$context->addError(
				'The %label% %path% expects to be %expected%, %value% given.',
				\nomit\Schema\Message::TYPE_MISMATCH,
				[
					'value' => $value,
					'expected' => implode('|', array_unique($expecteds)),
				],
			);
		}
	}


	public function completeDefault(Context $context)
	{
		if ($this->required) {
			$context->addError(
				'The mandatory item %path% is missing.',
				\nomit\Schema\Message::MISSING_ITEM,
			);
			return null;
		}

		if ($this->default instanceof Schema) {
			return $this->default->completeDefault($context);
		}

		return $this->default;
	}
}
