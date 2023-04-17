<?php

namespace nomit\Toasting\Filter\Specification;

use nomit\Toasting\Envelope\EnvelopeInterface;

interface SpecificationInterface
{

    public function isSatisfiedBy(EnvelopeInterface $envelope): bool;

}