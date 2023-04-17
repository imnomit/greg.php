<?php

namespace Dependencies\Amp\Http\Client\Connection;

use Dependencies\Amp\CancellationToken;
use Dependencies\Amp\Http\Client\Request;
use Dependencies\Amp\Promise;

interface ConnectionPool
{
    /**
     * Reserve a stream for a particular request.
     *
     * @param Request           $request
     * @param CancellationToken $cancellation
     *
     * @return Promise<Stream>
     */
    public function getStream(Request $request, CancellationToken $cancellation): Promise;
}
