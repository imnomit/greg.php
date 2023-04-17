<?php

namespace nomit\Messenger\Utilities;

use nomit\Messenger\Exception\InvalidArgumentException;
use nomit\Messenger\Message\MessageInterface;
use nomit\Messenger\Message\QueueableMessageInterface;
use nomit\Work\Callback;

class MessengerUtilities
{

    public static function guessQueue(MessageInterface $message): string
    {
        if($message instanceof QueueableMessageInterface) {
            return $message->getQueue();
        }

        return strtolower(trim(preg_replace('/[A-Z]/', '-\\0', $message->getName()), '-'));
    }

    public static function validateInput(string $caller, $input): mixed
    {
        if (\is_resource($input)) {
            return $input;
        }

        if (\is_string($input)) {
            return $input;
        }

        if (is_scalar($input)) {
            return (string) $input;
        }

        if ($input instanceof \Iterator) {
            return $input;
        }

        if ($input instanceof \Traversable) {
            return new \IteratorIterator($input);
        }

        throw new InvalidArgumentException(sprintf('The "%s" caller only accepts strings, "%s" objects or stream resources.', $caller, \Traversable::class));
    }

}