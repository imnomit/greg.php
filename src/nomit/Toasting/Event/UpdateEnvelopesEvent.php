<?php

namespace nomit\Toasting\Event;

use nomit\EventDispatcher\Event;

final class UpdateEnvelopesEvent extends Event
{

    public function __construct(
        private array $envelopes
    )
    {
    }

    /**
     * @param array $envelopes
     */
    public function setEnvelopes(array $envelopes): self
    {
        $this->envelopes = $envelopes;

        return $this;
    }

    /**
     * @return array
     */
    public function getEnvelopes(): array
    {
        return $this->envelopes;
    }

}