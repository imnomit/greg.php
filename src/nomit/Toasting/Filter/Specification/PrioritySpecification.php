<?php

namespace nomit\Toasting\Filter\Specification;

use nomit\Toasting\Envelope\EnvelopeInterface;
use nomit\Toasting\Stamp\PriorityStamp;

final class PrioritySpecification implements SpecificationInterface
{

    public function __construct(
        private int $minimumPriority,
        private ?int $maximumPriority = null
    )
    {
    }

    public function isSatisfiedBy(EnvelopeInterface $envelope): bool
    {
        $stamp = $envelope->get(PriorityStamp::class);

        if(!$stamp instanceof PriorityStamp) {
            return false;
        }

        if(null !== $this->maximumPriority && $stamp->getPriority() > $this->maximumPriority) {
            return false;
        }

        return $stamp->getPriority() >= $this->minimumPriority;
    }
}