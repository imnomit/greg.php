<?php

namespace nomit\Tree\Secure\Traits;

use nomit\Tree\Traits\LookUpTrait as Source;

/**
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
trait LookUpTrait
{

    use Source {
        getChildren as protected;
        descendant as protected;
        ancestor as protected;
        child as protected;
        sibling as protected;
        children as protected;
        ancestors as protected;
        descendants as protected;
        siblings as protected;
        findRootNode as protected;
    }
}

