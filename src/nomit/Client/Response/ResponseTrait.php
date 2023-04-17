<?php

namespace nomit\Client\Response;

use nomit\Client\Exception\BadMethodCallException;
use nomit\Client\Exception\ClientException;
use nomit\Client\Exception\JsonException;
use nomit\Client\Exception\RedirectionException;
use nomit\Client\Exception\ServerException;
use nomit\Client\Exception\TransportException;
use function William\dump;

trait ResponseTrait
{

    protected $initializer;

    protected ?bool $should_buffer;

    protected $content;

    protected int $offset = 0;

    protected array|string $json_data;

    protected static function initialize(self $response): void
    {
        if (null !== $response->getInformation('error')) {
            throw new TransportException($response->getInformation('error'));
        }

        try {
            if (($response->initializer)($response, -0.0)) {
                foreach (self::stream([$response], -0.0) as $chunk) {
                    if ($chunk->isFirst()) {
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Persist timeouts thrown during initialization
            $response->info['error'] = $e->getMessage();
            $response->close();

            throw $e;
        }

        $response->initializer = null;
    }

    public function __sleep(): array
    {
        throw new BadMethodCallException(sprintf('The "%s" object cannot be serialized.', __CLASS__));
    }

    public function __wakeup()
    {
        throw new BadMethodCallException(sprintf('The "%s" object cannot be unserialized.', __CLASS__));
    }

    public function getContent(bool $throw = true): string
    {
        if ($this->initializer) {
            self::initialize($this);
        }

        if ($throw) {
            $this->checkStatusCode();
        }

        if (null === $this->content) {
            $content = null;

            foreach (self::stream([$this]) as $chunk) {
                if (!$chunk->isLast()) {
                    $content .= $chunk->getContent();
                }
            }

            if (null !== $content) {
                return $content;
            }

            if (null === $this->content) {
                throw new TransportException('Cannot get the content of the response twice: buffering is disabled.');
            }
        } else {
            foreach (self::stream([$this]) as $chunk) {
                // Chunks are buffered in $this->content already
            }
        }

        rewind($this->content);

        return stream_get_contents($this->content);
    }

    public function toStream(bool $throw = true)
    {
        if ($throw) {
            // Ensure headers arrived
            $this->getHeaders($throw);
        }

        $stream = Streamer::createResource($this);

        stream_get_meta_data($stream)['wrapper_data']
            ->bindHandles($this->handle, $this->content);

        return $stream;
    }

    protected function checkStatusCode(): void
    {
        $code = $this->getInformation('http_code');

        if (500 <= $code) {
            throw new ServerException($this);
        }

        if (400 <= $code) {
            throw new ClientException($this);
        }

        if (300 <= $code) {
            throw new RedirectionException($this);
        }
    }

    abstract protected function close(): void;

    public function toArray(bool $throw = true): array
    {
        if ('' === $content = $this->getContent($throw)) {
            throw new JsonException('Response body is empty.');
        }

        if (null !== $this->json_data) {
            return $this->json_data;
        }

        try {
            $content = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING | (\PHP_VERSION_ID >= 70300 ? \JSON_THROW_ON_ERROR : 0));
        } catch (\JsonException $e) {
            throw new JsonException($e->getMessage().sprintf(' for "%s".', $this->getInformation('url')), $e->getCode());
        }

        if (\PHP_VERSION_ID < 70300 && \JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(json_last_error_msg().sprintf(' for "%s".', $this->getInformation('url')), json_last_error());
        }

        if (!\is_array($content)) {
            throw new JsonException(sprintf('JSON content was expected to decode to an array, "%s" returned for "%s".', get_debug_type($content), $this->getInformation('url')));
        }

        if (null !== $this->content) {
            // Option "buffer" is true
            return $this->json_data = $content;
        }

        return $content;
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}