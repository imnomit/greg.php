<?php

namespace nomit\Messenger\Handler;

use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Exception\InvalidArgumentException;
use nomit\Work\CallbackInterface;

class CallbackHandler extends AbstractHandler
{

    public const CALLBACK_DELIMITER = '::';

    public function __construct(array|string $callback)
    {
        if(is_string($callback)) {
            $callback = explode(self::CALLBACK_DELIMITER, $callback);
        }

        $count = count($callback);

        if($count < 2) {
            throw new InvalidArgumentException(sprintf('The supplied callback array is invalid, as its length, "%s", is less than the expected two.', $count));
        }

        [$object, $method] = $callback;

        if(!method_exists($object, $method)) {
            throw new InvalidArgumentException(sprintf('The supplied callback array is invalid, as the specified method, "%s", does not exist on the supplied object, "%s".', $method, get_class($object)));
        }

        if(!is_callable($callback)) {
            throw new InvalidArgumentException('The supplied callback array is not callable, and is thus invalid.');
        }

        $this->job = [$object, $method];
    }

    public function run(CallbackInterface $process, EnvelopeInterface $envelope): int
    {
        return call_user_func($this->job, $process, $envelope);
    }

}