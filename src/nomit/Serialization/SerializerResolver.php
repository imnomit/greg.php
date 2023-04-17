<?php

namespace nomit\Serialization;

use nomit\Dumper\Dumper;
use nomit\Exception\InvalidArgumentException;
use nomit\Serialization\Encoder\EncoderInterface;
use nomit\Serialization\Exception\UnsupportedFormatSerializerException;
use nomit\Serialization\Transformer\TransformerInterface;
use nomit\Utility\Arrays;

class SerializerResolver implements SerializerResolverInterface
{

    private array $serializers;

    public function __construct(
        array $encoders,
        array $transformers,
    )
    {
        $this->serializers = array_merge($encoders, $transformers);
    }

    public function push(SerializerInterface $serializer): self
    {
        $this->serializers[] = $serializer;

        return $this;
    }

    public function serialize(mixed $value, string $format, array $context = []): string
    {
        return $this->getSerializer($value, $format)->serialize($value, $context);
    }

    public function unserialize(mixed $value, string $format, array $context = []): mixed
    {
        return $this->getSerializer($value, $format)->unserialize($value, $context);
    }

    protected function getSerializer(mixed $value, string $format): SerializerInterface
    {
        foreach($this->serializers as $serializer) {
            if(($serializer instanceof EncoderInterface) && $this->supports($serializer, $format)) {
                return new Serializer($serializer);
            }

            if($serializer instanceof TransformerInterface && $serializer->supports($value)) {
                return new TransformingSerializer($serializer);
            }
        }

        throw new UnsupportedFormatSerializerException(
            new InvalidArgumentException(
                sprintf('The supplied serialization format, "%s", is not supported by any of the registered serializers. The supported formats are: "%s".', $format, implode(', ', $this->getSupportedFormats()))
            )
        );
    }

    private function getSupportedFormats(): array
    {
        $formats = [];

        foreach($this->serializers as $serializer) {
            if($serializer instanceof EncoderInterface) {
                $formats[] = $serializer->supports();
            }
        }

        return array_unique($formats);
    }

    private function supports(EncoderInterface $encoder, string $format): bool
    {
        $format = strtolower($format);
        $supported = $encoder->supports();

        if($supported === '*') {
            return true;
        }

        return $supported === $format;
    }

    private function getCacheKey(mixed $value): string
    {
        return md5(serialize($value));
    }

}