<?php

namespace nomit\Serialization\Transformer;

use nomit\Serialization\SerializerInterface;

interface TransformerInterface extends SerializerInterface
{

    public function supports(mixed $value): bool;

}