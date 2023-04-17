<?php

namespace Dependencies\Amp\Http\Tunnel;

use Dependencies\Amp\CancellationToken;
use Dependencies\Amp\Http\Client\Connection\Http1Connection;
use Dependencies\Amp\Http\Client\Connection\Stream;
use Dependencies\Amp\Http\Client\Internal\ForbidCloning;
use Dependencies\Amp\Http\Client\Internal\ForbidSerialization;
use Dependencies\Amp\Http\Client\Request;
use Dependencies\Amp\Http\Client\Response;
use Dependencies\Amp\NullCancellationToken;
use Dependencies\Amp\Promise;
use Dependencies\Amp\Socket\ConnectContext;
use Dependencies\Amp\Socket\ConnectException;
use Dependencies\Amp\Socket\Connector;
use Dependencies\Amp\Socket\EncryptableSocket;
use Dependencies\Amp\Socket\SocketAddress;
use function Amp\call;
use function Amp\Socket\connector;

final class Http1TunnelConnector implements Connector
{
    use ForbidCloning;
    use ForbidSerialization;

    public static function tunnel(
        EncryptableSocket $socket,
        string $target,
        array $customHeaders,
        CancellationToken $cancellationToken
    ): Promise {
        return call(static function () use ($socket, $target, $customHeaders, $cancellationToken) {
            $request = new Request('http://' . \str_replace('tcp://', '', $target), 'CONNECT');
            $request->setHeaders($customHeaders);
            $request->setUpgradeHandler(static function (EncryptableSocket $socket) use (&$upgradedSocket) {
                $upgradedSocket = $socket;
            });

            $connection = new Http1Connection($socket, 1000);

            /** @var Stream $stream */
            $stream = yield $connection->getStream($request);

            /** @var Response $response */
            $response = yield $stream->request($request, $cancellationToken);

            if ($response->getStatus() !== 200) {
                throw new ConnectException('Failed to connect to proxy: Received a bad status code (' . $response->getStatus() . ')');
            }

            \assert($upgradedSocket !== null);

            return $upgradedSocket;
        });
    }

    /** @var string */
    private $proxyUri;
    /** @var array */
    private $customHeaders;
    /** @var Connector|null */
    private $connector;

    public function __construct(SocketAddress $proxyAddress, array $customHeaders = [], ?Connector $connector = null)
    {
        $this->proxyUri = (string) $proxyAddress;
        $this->customHeaders = $customHeaders;
        $this->connector = $connector;
    }

    public function connect(string $uri, ?ConnectContext $context = null, ?CancellationToken $token = null): Promise
    {
        return call(function () use ($uri, $context, $token) {
            $connector = $this->connector ?? connector();

            $socket = yield $connector->connect($this->proxyUri, $context, $token);

            return self::tunnel($socket, $uri, $this->customHeaders, $token ?? new NullCancellationToken);
        });
    }
}
