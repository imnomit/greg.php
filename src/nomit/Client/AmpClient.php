<?php

namespace nomit\Client;

use Dependencies\Amp\CancelledException;
use Dependencies\Amp\Http\Tunnel\Http1TunnelConnector;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use nomit\Utility\Service\ResetInterface;
use nomit\Client\Exception\TransportException;
use nomit\Client\Response\AmpResponse;
use nomit\Client\Response\ResponseInterface;
use nomit\Client\Response\ResponseStream;
use nomit\Client\Response\ResponseStreamInterface;
use nomit\Client\Support\Amp\AmpClientState;
use nomit\Web\Request\Request;

final class AmpClient implements ClientInterface, LoggerAwareInterface, ResetInterface
{

    use ClientTrait,
        LoggerAwareTrait;

    protected static array $empty_defaults = self::OPTIONS_DEFAULTS;

    protected array $default_options = self::OPTIONS_DEFAULTS;

    protected AmpClientState $multi;

    public function __construct(array $defaultOptions = [], callable $clientConfigurator = null, int $maximumMostConnections = 6,
                                int $maximumPendingPushes = 50
    )
    {
        $this->default_options['buffer'] = $this->default_options['buffer'] ?? \Closure::fromCallable([__CLASS__, 'shouldBuffer']);

        if ($defaultOptions) {
            [, $this->default_options] = self::prepareRequest(null, null, $defaultOptions, $this->default_options);
        }

        $this->multi = new AmpClientState($clientConfigurator, $maximumPendingPushes, $maximumPendingPushes, $this->logger);
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        [$url, $options] = self::prepareRequest($method, $url, $options, $this->default_options);

        $options['proxy'] = self::getProxy($options['proxy'], $url, $options['no_proxy']);

        if (null !== $options['proxy'] && !class_exists(Http1TunnelConnector::class)) {
            throw new \LogicException('You cannot use the "proxy" option as the "amphp/http-tunnel" package is not installed. Try running "composer require amphp/http-tunnel".');
        }

        if ($options['bindto']) {
            if (0 === strpos($options['bindto'], 'if!')) {
                throw new TransportException(__CLASS__.' cannot bind to network interfaces, use e.g. CurlHttpClient instead.');
            }
            if (0 === strpos($options['bindto'], 'host!')) {
                $options['bindto'] = substr($options['bindto'], 5);
            }
        }

        if (('' !== $options['body'] || 'POST' === $method || isset($options['normalized_headers']['content-length'])) && !isset($options['normalized_headers']['content-type'])) {
            $options['headers'][] = 'Content-Type: application/x-www-form-urlencoded';
        }

        if (!isset($options['normalized_headers']['user-agent'])) {
            $options['headers'][] = 'User-Agent: Symfony HttpClient/Amp';
        }

        if (0 < $options['max_duration']) {
            $options['timeout'] = min($options['max_duration'], $options['timeout']);
        }

        if ($options['resolve']) {
            $this->multi->dnsCache = $options['resolve'] + $this->multi->dns_cache;
        }

        if ($options['peer_fingerprint'] && !isset($options['peer_fingerprint']['pin-sha256'])) {
            throw new TransportException(__CLASS__.' supports only "pin-sha256" fingerprints.');
        }

        $request = new \Dependencies\Amp\Http\Client\Request(implode('', $url), $method);

        if ($options['http_version']) {
            switch ((float) $options['http_version']) {
                case 1.0: $request->setProtocolVersions(['1.0']); break;
                case 1.1: $request->setProtocolVersions(['1.1', '1.0']); break;
                default: $request->setProtocolVersions(['2', '1.1', '1.0']); break;
            }
        }

        foreach ($options['headers'] as $v) {
            $h = explode(': ', $v, 2);
            $request->addHeader($h[0], $h[1]);
        }

        $request->setTcpConnectTimeout(1000 * $options['timeout']);
        $request->setTlsHandshakeTimeout(1000 * $options['timeout']);
        $request->setTransferTimeout(1000 * $options['max_duration']);
        if (method_exists($request, 'setInactivityTimeout')) {
            $request->setInactivityTimeout(0);
        }

        if ('' !== $request->getUri()->getUserInfo() && !$request->hasHeader('authorization')) {
            $auth = explode(':', $request->getUri()->getUserInfo(), 2);
            $auth = array_map('rawurldecode', $auth) + [1 => ''];
            $request->setHeader('Authorization', 'Basic '.base64_encode(implode(':', $auth)));
        }

        return new AmpResponse($this->multi, $request, $options, $this->logger);
    }

    public function stream(iterable|ResponseInterface $responses, float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof AmpResponse) {
            $responses = [$responses];
        } elseif (!is_iterable($responses)) {
            throw new \TypeError(sprintf('"%s()" expects parameter 1 to be an iterable of AmpResponse objects, "%s" given.', __METHOD__, get_debug_type($responses)));
        }

        return new ResponseStream(AmpResponse::stream($responses, $timeout));
    }

    public function reset()
    {
        $this->multi->dns_cache = [];

        foreach ($this->multi->pushed_responses as $authority => $pushedResponses) {
            foreach ($pushedResponses as [$pushedUrl, $pushDeferred]) {
                $pushDeferred->fail(new CancelledException());

                if ($this->logger) {
                    $this->logger->debug(sprintf('Unused pushed response: "%s"', $pushedUrl));
                }
            }
        }

        $this->multi->pushed_responses = [];
    }

}