<?php

namespace Dependencies\Amp\Http\Client\Interceptor;

use Dependencies\Amp\Http\Client\Response;

final class RemoveResponseHeader extends ModifyResponse
{
    public function __construct(string $headerName)
    {
        parent::__construct(static function (Response $response) use ($headerName) {
            $response->removeHeader($headerName);

            return $response;
        });
    }
}
