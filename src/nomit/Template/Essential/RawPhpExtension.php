<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Template\Essential;

use nomit\Template;


/**
 * Raw PHP in {php ...}
 */
final class RawPhpExtension extends \nomit\Template\Extension
{
	use \nomit\Template\Strict;

	public function getTags(): array
	{
		return [
			'php' => [Nodes\RawPhpNode::class, 'create'],
		];
	}
}
