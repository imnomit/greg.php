<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Template\Compiler\Nodes\Php\Expression;

use nomit\Template\Compiler\Nodes\Php;
use nomit\Template\Compiler\Nodes\Php\ExpressionNode;
use nomit\Template\Compiler\Position;
use nomit\Template\Compiler\PrintContext;


class FilterCallNode extends ExpressionNode
{
	public function __construct(
		public ExpressionNode $expr,
		public Php\FilterNode $filter,
		public ?Position $position = null,
	) {
	}


	public function print(PrintContext $context): string
	{
		return $this->filter->printSimple($context, $this->expr->print($context));
	}


	public function &getIterator(): \Generator
	{
		yield $this->expr;
		yield $this->filter;
	}
}
