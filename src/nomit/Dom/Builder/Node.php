<?php

namespace nomit\Dom\Builder;


use nomit\Tree\Secure\Traits\ChildrenTrait;
use nomit\Tree\Secure\Traits\LookUpTrait;
use nomit\Utility\Concern\Stringable;


/**
 *
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
abstract class Node implements Stringable
{
	
	use ChildrenTrait;
	use LookUpTrait;

	protected function childNodesToString()
	{
		$html = '';
		foreach ($this->getChildren() as $child) {
			$html .= $child->toString();
		}
		return $html;
	}

	abstract function toString(): string;

	public function __toString(): string
	{
		return $this->toString();
	}

}

