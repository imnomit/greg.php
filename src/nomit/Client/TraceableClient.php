<?php

namespace nomit\Client;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use nomit\StopWatch\Stopwatch;
use nomit\Utility\ArrayObject;
use nomit\Utility\Service\ResetInterface;
use nomit\Client\Response\ResponseInterface;
use nomit\Client\Response\ResponseStream;
use nomit\Client\Response\ResponseStreamInterface;
use nomit\Client\Response\TraceableResponse;

final class TraceableClient implements ClientInterface, ResetInterface, LoggerAwareInterface
{

    protected ClientInterface $client;

    protected ?Stopwatch $stopwatch;

    protected ArrayObject $traced_requests;

    public function __construct(ClientInterface $client, Stopwatch $stopwatch = null)
    {
        $this->client = $client;
        $this->stopwatch = $stopwatch;
        $this->traced_requests = new ArrayObject();
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $content = null;
        $traceInfo = [];
        $this->traced_requests[] = [
            'method' => $method,
            'url' => $url,
            'options' => $options,
            'info' => &$traceInfo,
            'content' => &$content,
        ];
        $onProgress = $options['on_progress'] ?? null;

        if (false === ($options['extra']['trace_content'] ?? true)) {
            unset($content);
            $content = false;
        }

        $options['on_progress'] = function (int $dlNow, int $dlSize, array $info) use (&$traceInfo, $onProgress) {
            $traceInfo = $info;

            if (null !== $onProgress) {
                $onProgress($dlNow, $dlSize, $info);
            }
        };

        return new TraceableResponse($this->client, $this->client->request($method, $url, $options), $content, null === $this->stopwatch ? null : $this->stopwatch->start("$method $url", 'http_client'));
    }

    public function stream(iterable|ResponseInterface $responses, float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof TraceableResponse) {
            $responses = [$responses];
        } elseif (!is_iterable($responses)) {
            throw new \TypeError(sprintf('"%s()" expects parameter 1 to be an iterable of TraceableResponse objects, "%s" given.', __METHOD__, get_debug_type($responses)));
        }

        return new ResponseStream(TraceableResponse::stream($this->client, $responses, $timeout));
    }

    public function getTracedRequests(): array
    {
        return $this->traced_requests->getArrayCopy();
    }

    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }

        $this->traced_requests->exchangeArray([]);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }

        $this->traced_requests->exchangeArray([]);
    }

    public function withOptions(array $options): ClientInterface
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);

        return $clone;
    }

}