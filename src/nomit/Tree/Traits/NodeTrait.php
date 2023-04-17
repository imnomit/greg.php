<?php

namespace nomit\Tree\Traits;

/**
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
trait NodeTrait
{

    use AttributesTrait;
    use ChildrenTrait;
    use LookUpTrait;
    use ToStringTrait;
}
