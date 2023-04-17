<?php

declare(strict_types=1);

namespace nomit\Template\Compiler;

use nomit\Template;


/**
 * @implements \IteratorAggregate<Node>
 */
abstract class Node implements \IteratorAggregate
{
	use \nomit\Template\Strict;

	public ?Position $position = null;


	abstract public function print(PrintContext $context): string;


	public function &getIterator(): \Generator
	{
		return;
		yield;
	}
}
