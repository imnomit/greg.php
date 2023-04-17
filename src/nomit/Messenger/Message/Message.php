<?php

namespace nomit\Messenger\Message;

class Message extends AbstractMessage implements QueueableMessageInterface
{

    public function getQueue(): string
    {
        return strtolower(trim(preg_replace('/[A-Z]/', '-\\0', $this->getName()), '-'));
    }

}