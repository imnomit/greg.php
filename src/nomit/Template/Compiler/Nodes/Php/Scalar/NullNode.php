<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Template\Compiler\Nodes\Php\Scalar;

use nomit\Template\Compiler\Nodes\Php\ScalarNode;
use nomit\Template\Compiler\Position;
use nomit\Template\Compiler\PrintContext;


class NullNode extends ScalarNode
{
	public function __construct(
		public ?Position $position = null,
	) {
	}


	public function print(PrintContext $context): string
	{
		return 'null';
	}
}
