<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace nomit\Template\Compiler\Nodes\Html;

use nomit\Template\Compiler\Nodes\AreaNode;
use nomit\Template\Compiler\Position;
use nomit\Template\Compiler\PrintContext;


/**
 * HTML bogus tag.
 */
class BogusTagNode extends AreaNode
{
	public function __construct(
		public string $openDelimiter,
		public AreaNode $content,
		public string $endDelimiter,
		public ?Position $position = null,
	) {
	}


	public function print(PrintContext $context): string
	{
		$res = 'echo ' . var_export($this->openDelimiter, true) . ';';
		$context->beginEscape()->enterHtmlBogusTag();
		$res .= $this->content->print($context);
		$context->restoreEscape();
		$res .= 'echo ' . var_export($this->endDelimiter, true) . ';';
		return $res;
	}


	public function &getIterator(): \Generator
	{
		yield $this->content;
	}
}
