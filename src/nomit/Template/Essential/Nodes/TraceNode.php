<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Template\Essential\Nodes;

use nomit\Template\Compiler\Nodes\StatementNode;
use nomit\Template\Compiler\PrintContext;
use nomit\Template\Compiler\Tag;


/**
 * {trace}
 */
class TraceNode extends StatementNode
{
	public static function create(Tag $tag): static
	{
		return new static;
	}


	public function print(PrintContext $context): string
	{
		return $context->format(
			'nomit\Template\Essential\Tracer::throw() %line;',
			$this->position,
		);
	}
}
