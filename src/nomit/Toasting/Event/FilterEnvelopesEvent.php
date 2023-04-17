<?php

namespace nomit\Toasting\Event;

use nomit\EventDispatcher\Event;
use nomit\Toasting\Filter\Filter;
use nomit\Toasting\Filter\FilterInterface;

final class FilterEnvelopesEvent extends Event
{

    private FilterInterface $filter;

    public function __construct(
        array $envelopes,
        array $criteria
    )
    {
        $this->filter = new Filter($envelopes, $criteria);
    }

    public function getEnvelopes(): array
    {
        return $this->filter->getResult();
    }

    /**
     * @param FilterInterface $filter
     */
    public function setFilter(FilterInterface $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return FilterInterface
     */
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

}