<?php

namespace nomit\Console\Format\Color;

interface ColorInterface
{

    public function apply(string $text): string;

    public function set(): string;

    public function unset(): string;

}