<?php

namespace nomit\Drive\Plugin;

use nomit\Utility\Concern\Stringable;

interface PluginInterface extends Stringable
{

    public function getName(): string;

}