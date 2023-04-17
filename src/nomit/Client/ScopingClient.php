<?php

namespace nomit\Client;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use nomit\Utility\Service\ResetInterface;
use nomit\Client\Exception\InvalidArgumentException;
use nomit\Client\Response\ResponseInterface;
use nomit\Client\Response\ResponseStreamInterface;

class ScopingClient implements ClientInterface, ResetInterface, LoggerAwareInterface
{

    use ClientTrait;

    protected ?ClientInterface $client;

    protected array $default_options_by_regexp;

    protected ?string $default_regexp;

    public static function forBaseUri(ClientInterface $client, string $baseUri, array $defaultOptions = [], string $regexp = null): self
    {
        if (null === $regexp) {
            $regexp = preg_quote(implode('', self::resolveUrl(self::parseUrl('.'), self::parseUrl($baseUri))));
        }

        $defaultOptions['base_uri'] = $baseUri;

        return new self($client, [$regexp => $defaultOptions], $regexp);
    }

    public function __construct(ClientInterface $client, array $defaultOptionsByRegexp, string $defaultRegexp = null)
    {
        $this->client = $client;
        $this->default_options_by_regexp = $defaultOptionsByRegexp;
        $this->default_regexp = $defaultRegexp;

        if (null !== $defaultRegexp && !isset($defaultOptionsByRegexp[$defaultRegexp])) {
            throw new InvalidArgumentException(sprintf('No options are mapped to the provided "%s" default regexp.', $defaultRegexp));
        }
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $exception = null;
        $url = self::parseUrl($url, $options['query'] ?? []);

        if (\is_string($options['base_uri'] ?? null)) {
            $options['base_uri'] = self::parseUrl($options['base_uri']);
        }

        try {
            $url = implode('', self::resolveUrl($url, $options['base_uri'] ?? null));
        } catch (InvalidArgumentException $exception) {
            if (null === $this->default_regexp) {
                throw $exception;
            }

            $defaultOptions = $this->default_options_by_regexp[$this->default_regexp];
            $options = self::mergeDefaultOptions($options, $defaultOptions, true);
            if (\is_string($options['base_uri'] ?? null)) {
                $options['base_uri'] = self::parseUrl($options['base_uri']);
            }
            $url = implode('', self::resolveUrl($url, $options['base_uri'] ?? null, $defaultOptions['query'] ?? []));
        }

        foreach ($this->default_options_by_regexp as $regexp => $defaultOptions) {
            if (preg_match("{{$regexp}}A", $url)) {
                if (null === $exception || $regexp !== $this->default_regexp) {
                    $options = self::mergeDefaultOptions($options, $defaultOptions, true);
                }
                break;
            }
        }

        return $this->client->request($method, $url, $options);
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        if ($this->client instanceof LoggerAwareInterface) {
            $this->client->setLogger($logger);
        }
    }

    public function withOptions(array $options): self
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);

        return $clone;
    }
}