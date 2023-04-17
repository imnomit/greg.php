<?php

namespace nomit\Security\Session\Context;

use nomit\Security\Session\Storage\StorageInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Jsonable;
use nomit\Utility\Concern\Serializable;

interface SessionContextInterface extends StorageInterface, Arrayable, Jsonable, Serializable
{


}