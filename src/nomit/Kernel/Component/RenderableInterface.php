<?php

namespace nomit\Kernel\Component;

interface RenderableInterface
{

    public function redrawControl(): void;

    public function isControlInvalid(): bool;

}