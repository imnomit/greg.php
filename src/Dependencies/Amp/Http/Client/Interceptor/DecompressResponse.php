<?php

namespace Dependencies\Amp\Http\Client\Interceptor;

use Dependencies\Amp\ByteStream\ZlibInputStream;
use Dependencies\Amp\CancellationToken;
use Dependencies\Amp\Http\Client\Connection\Stream;
use Dependencies\Amp\Http\Client\Internal\ForbidCloning;
use Dependencies\Amp\Http\Client\Internal\ForbidSerialization;
use Dependencies\Amp\Http\Client\Internal\SizeLimitingInputStream;
use Dependencies\Amp\Http\Client\NetworkInterceptor;
use Dependencies\Amp\Http\Client\Request;
use Dependencies\Amp\Http\Client\Response;
use Dependencies\Amp\Promise;
use function Amp\call;

final class DecompressResponse implements NetworkInterceptor
{
    use ForbidCloning;
    use ForbidSerialization;

    /** @var bool */
    private $hasZlib;

    public function __construct()
    {
        $this->hasZlib = \extension_loaded('zlib');
    }

    public function requestViaNetwork(
        Request $request,
        CancellationToken $cancellation,
        Stream $stream
    ): Promise {
        // If a header is manually set, we won't interfere
        if ($request->hasHeader('accept-encoding')) {
            return $stream->request($request, $cancellation);
        }

        return call(function () use ($request, $cancellation, $stream) {
            $this->addAcceptEncodingHeader($request);

            $request->interceptPush(function (Response $response) {
                return $this->decompressResponse($response);
            });

            return $this->decompressResponse(yield $stream->request($request, $cancellation));
        });
    }

    private function addAcceptEncodingHeader(Request $request): void
    {
        if ($this->hasZlib) {
            $request->setHeader('Accept-Encoding', 'gzip, deflate, identity');
        }
    }

    private function decompressResponse(Response $response): Response
    {
        if (($encoding = $this->determineCompressionEncoding($response))) {
            $sizeLimit = $response->getRequest()->getBodySizeLimit();
            /** @noinspection PhpUnhandledExceptionInspection */
            $decompressedBody = new ZlibInputStream($response->getBody(), $encoding);

            $response->setBody(new SizeLimitingInputStream($decompressedBody, $sizeLimit));
            $response->removeHeader('content-encoding');
        }

        return $response;
    }

    private function determineCompressionEncoding(Response $response): int
    {
        if (!$this->hasZlib) {
            return 0;
        }

        if (!$response->hasHeader("content-encoding")) {
            return 0;
        }

        $contentEncoding = $response->getHeader("content-encoding");

        \assert($contentEncoding !== null);

        $contentEncodingHeader = \trim($contentEncoding);

        if (\strcasecmp($contentEncodingHeader, 'gzip') === 0) {
            return \ZLIB_ENCODING_GZIP;
        }

        if (\strcasecmp($contentEncodingHeader, 'deflate') === 0) {
            return \ZLIB_ENCODING_DEFLATE;
        }

        return 0;
    }
}
