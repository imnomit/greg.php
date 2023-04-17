<?php

namespace nomit\Messenger\Stamp;

class SerializerStamp implements StampInterface
{

    protected array $context;

    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    public function getName(): string
    {
        return 'SerializedStamp';
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

}