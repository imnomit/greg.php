<?php

namespace nomit\Serialization\Encoder;

use nomit\Exception\BadMethodCallException;
use nomit\Utility\Serialization\Markdown\ParserInterface;

final class MarkdownEncoder implements EncoderInterface
{

    public function __construct(
        private ParserInterface $parser
    )
    {
    }

    public function serialize(mixed $value, array $context = []): string
    {
        throw new BadMethodCallException(sprintf('The "%s" markdown encoder does not support serialization via the "%s" method.', get_class($this), __METHOD__));
    }

    public function unserialize(mixed $value, array $context = []): mixed
    {
        return $this->parser->parse($value);
    }

    public function supports(): string
    {
        return 'md';
    }

}