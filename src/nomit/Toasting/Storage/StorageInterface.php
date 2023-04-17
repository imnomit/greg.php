<?php

namespace nomit\Toasting\Storage;

use nomit\Toasting\Envelope\EnvelopeInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Serializable;

interface StorageInterface extends Arrayable, \Countable
{

    public function all(): array;

    public function add(EnvelopeInterface|array $envelopes): self;

    public function update(EnvelopeInterface|array $envelopes): self;

    public function remove(EnvelopeInterface|array $envelopes): void;

    public function clear(): void;

}