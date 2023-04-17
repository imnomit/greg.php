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


class AssignOpNode extends ExpressionNode
{
	private const Ops = ['+', '-', '*', '/', '.', '%', '&', '|', '^', '<<', '>>', '**', '??'];


	public function __construct(
		public ExpressionNode $var,
		public /*readonly*/ string $operator,
		public ExpressionNode $expr,
		public ?Position $position = null,
	) {
		if (!in_array($this->operator, self::Ops, true)) {
			throw new \InvalidArgumentException("Unexpected operator '$this->operator'");
		}
	}


	public function print(PrintContext $context): string
	{
		return $context->infixOp($this, $this->var, ' ' . $this->operator . '= ', $this->expr);
	}


	public function &getIterator(): \Generator
	{
		yield $this->var;
		yield $this->expr;
	}
}
