<?php

namespace nomit\Utility\Concern;

interface Integerable extends Stringable
{

    public function toInteger(): int;

}