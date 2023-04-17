<?php

namespace nomit\Serialization\Encoder;

use nomit\Utility\Serialization\Yaml\Dumper;
use nomit\Utility\Serialization\Yaml\Parser;
use nomit\Utility\Serialization\Yaml\YamlParser;

final class YamlEncoder implements EncoderInterface
{

    public const FORMAT = 'yaml';

    public const PRESERVE_EMPTY_OBJECTS = 'preserve_empty_objects';

    public const YAML_INLINE = 'yaml_inline';
    public const YAML_INDENT = 'yaml_indent';
    public const YAML_FLAGS = 'yaml_flags';

    public function __construct(
        private ?Dumper $dumper = null,
        private ?Parser $parser = null,
        private array $defaultContext = [
            self::YAML_INLINE => 0,
            self::YAML_INDENT => 0,
            self::YAML_FLAGS => 0,
        ]
    )
    {
        if(!$this->dumper) {
            $this->dumper = new Dumper();
        }

        if(!$this->parser) {
            $this->parser = new Parser();
        }
    }

    public function serialize(mixed $value, array $context = []): string
    {
        $context = array_merge($this->defaultContext, $context);

        if (isset($context[self::PRESERVE_EMPTY_OBJECTS])) {
            $context[self::YAML_FLAGS] |= YamlParser::DUMP_OBJECT_AS_MAP;
        }

        return $this->dumper->dump($value, $context[self::YAML_INLINE], $context[self::YAML_INDENT], $context[self::YAML_FLAGS]);
    }

    public function unserialize(mixed $value, array $context = []): array
    {
        $context = array_merge($this->defaultContext, $context);

        return $this->parser->parse($value, $context[self::YAML_FLAGS]);
    }

    public function supports(): string
    {
        return 'yaml';
    }

}