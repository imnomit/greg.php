<?php

namespace nomit\Serialization\Transformer;

class PhpTransformer extends ArrayTransformer
{

    public function serialize(mixed $value, array $context = []): string
    {
        return serialize(parent::serialize($value, $context));
    }

}