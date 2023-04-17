<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Template\Compiler\Nodes\Php\Expression;

use nomit\Template\Compiler\Nodes\Php\ExpressionNode;
use nomit\Template\Compiler\Position;
use nomit\Template\Compiler\PrintContext;


class PostOpNode extends ExpressionNode
{
	private const Ops = ['++' => 1, '--' => 1];


	public function __construct(
		public ExpressionNode $var,
		public /*readonly*/ string $operator,
		public ?Position $position = null,
	) {
		if (!isset(self::Ops[$this->operator])) {
			throw new \InvalidArgumentException("Unexpected operator '$this->operator'");
		}
	}


	public function print(PrintContext $context): string
	{
		return $context->postfixOp($this, $this->var, $this->operator);
	}


	public function &getIterator(): \Generator
	{
		yield $this->var;
	}
}
