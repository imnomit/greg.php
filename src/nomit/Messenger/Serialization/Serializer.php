<?php

namespace nomit\Messenger\Serialization;

use nomit\Messenger\Envelope\Envelope;
use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Exception\ExceptionInterface;
use nomit\Messenger\Exception\MessageUnserializationFailureException;
use nomit\Messenger\Stamp\SerializerStamp;
use nomit\Messenger\Stamp\UnsendableStampInterface;
use nomit\Serialization\SerializerResolver;
use nomit\Serialization\SerializerResolverInterface;


class Serializer implements SerializerInterface
{

    public const MESSENGER_SERIALIZATION_CONTEXT = 'msg_context';
    private const STAMP_HEADER_PREFIX = 'X-Message-Stamp-';

    protected array $context;

    public function __construct(
        private SerializerResolverInterface $resolver,
        private string $format = 'json',
        array $context = []
    )
    {
        $this->context = $context + [self::MESSENGER_SERIALIZATION_CONTEXT => true];
    }

    public function serialize(EnvelopeInterface $envelope, array $context = []): string
    {
        return $this->resolver->serialize($this->encode($envelope, $context), $this->format);
    }

    public function encode(EnvelopeInterface $envelope, array $context = []): array
    {
        $context = array_merge($this->context, $context);

        if($serializerStamp = $envelope->last(SerializerStamp::class)) {
            $context = $serializerStamp->getContext() + $context;
        }

        $envelope = $envelope->without(UnsendableStampInterface::class);

        $headers = array_merge([
            'type' => get_class($envelope->getMessage())
        ], $this->encodeStamps($envelope), $this->getContentTypeHeader());

        return [
            'body' => $this->resolver->serialize($envelope->getMessage(), $this->format, $context),
            'headers' => $headers,
        ];
    }

    protected function encodeStamps(EnvelopeInterface $envelope): array
    {
        if(!$allStamps = $envelope->all()) {
            return [];
        }

        $headers = [];

        foreach($allStamps as $class => $stamps) {
            $headers[self::STAMP_HEADER_PREFIX.$class] = $this->resolver->serialize($stamps, $this->format, $this->context);
        }

        return $headers;
    }

    public function unserialize(string $payload, array $context = []): ?EnvelopeInterface
    {
        $data = $this->resolver->unserialize($payload, $this->format);

        return $this->decode($data, $context);
    }

    public function decode(array $payload, array $context = []): ?EnvelopeInterface
    {
        if (empty($payload['body']) || empty($payload['headers'])) {
            throw new MessageUnserializationFailureException('A serialized envelope payload must have at least a "body" and one or more "headers".');
        }

        if (empty($payload['headers']['type'])) {
            throw new MessageUnserializationFailureException('The supplied serialized envelope payload does not have the necessary "type" header.');
        }

        $stamps = $this->decodeStamps($payload);
        $serializerStamp = $this->getSerializerStamp($stamps);
        $context = array_merge($this->context, $context);

        if(null !== $serializerStamp) {
            $context = array_merge($serializerStamp->getContext(), $context);
        }

        try {
            $message = $this->resolver->unserialize($payload['body'], $this->format, $context);
        } catch(ExceptionInterface $exception) {
            throw new MessageUnserializationFailureException(sprintf('An error occurred while attempting to deserialize the supplied envelope payload: "%s".', $exception->getMessage()), $exception->getCode(), $exception);
        }

        return new Envelope($message, $stamps);
    }

    protected function decodeStamps(array $payload): array
    {
        $stamps = [];

        foreach($payload['headers'] as $name => $value) {
            if (!str_starts_with($name, self::STAMP_HEADER_PREFIX)) {
                continue;
            }

            try {
                $stamps[] = $this->resolver->unserialize($value, $this->format, $this->context);
            } catch (ExceptionInterface $exception) {
                throw new MessageUnserializationFailureException(sprintf('An error occurred while decoding a messenger stamp: "%s".', $exception->getMessage()), $exception->getCode(), $exception);
            }
        }

        if($stamps) {
            $stamps = array_merge(...$stamps);
        }

        return $stamps;
    }

    protected function getSerializerStamp(array $stamps): ?SerializerStamp
    {
        foreach($stamps as $stamp) {
            if($stamp instanceof SerializerStamp) {
                return $stamp;
            }
        }

        return null;
    }

    protected function getContentTypeHeader(): array
    {
        $mimeType = $this->getFormatMimeType();

        return null === $mimeType ? [] : ['Content-Type' => $mimeType];
    }

    protected function getFormatMimeType(): ?string
    {
        switch ($this->format) {
            case 'json':
                return 'application/json';
            case 'xml':
                return 'application/xml';
            case 'yml':
            case 'yaml':
                return 'application/x-yaml';
            case 'csv':
                return 'text/csv';
        }

        return null;
    }

}