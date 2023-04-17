<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Template\Bridges\Kernel;


use nomit\Exception\InvalidStateException;
use nomit\Kernel\Component\ControlInterface;
use nomit\Kernel\Component\ControllerInterface;
use nomit\Kernel\Controller\Control;
use nomit\Kernel\Controller\PresenterInterface;
use nomit\Security\User\UserInterface;
use nomit\Utility\Arrays;

/**
 * Default template for controls and presenters.
 *
 * @method bool isLinkCurrent(string $destination = null, ...$args)
 * @method bool isModuleCurrent(string $module)
 */
#[\AllowDynamicProperties]
final class DefaultTemplate extends Template
{
	public ControllerInterface $controller;
	public ControlInterface $control;
	public UserInterface $user;
	public string $baseUrl;
	public string $basePath;

	/** @var \stdClass[] */
	public array $flashes = [];


	/**
	 * Adds new template parameter.
	 */
	public function add(string $name, $value): static
	{
		if (property_exists($this, $name)) {
			throw new InvalidStateException("The variable '$name' already exists.");
		}

		$this->$name = $value;
		return $this;
	}


	/**
	 * Sets all parameters.
	 */
	public function setParameters(array $params): static
	{
		return Arrays::toObject($params, $this);
	}
}
