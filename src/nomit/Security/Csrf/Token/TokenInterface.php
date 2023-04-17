<?php

namespace nomit\Security\Csrf\Token;

use nomit\Utility\Concern\Stringable;

interface TokenInterface extends Stringable
{

    public function getId(): string;

    public function getValue(): string;

}