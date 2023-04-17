<?php

namespace nomit\Notification\Filter;

use nomit\Notification\Filter\Specification\AndSpecification;
use nomit\Notification\Filter\Specification\SpecificationInterface;
use nomit\Notification\Notification\NotificationInterface;
use nomit\Notification\Stamp\OrderableStampInterface;
use Psr\Log\LoggerInterface;

final class Filter implements FilterInterface
{

    private ?SpecificationInterface $specification = null;

    private array $orderings = [];

    private ?int $maximumResults = null;

    public function __construct(
        private array $envelopes,
        private array $criteria,
        private ?LoggerInterface $logger = null
    )
    {
    }

    public function getResult(): array
    {
        $criteriaBuilder = new CriteriaBuilder($this, $this->criteria, $this->logger);
        $criteriaBuilder->build();

        $this->applySpecification();
        $this->applyOrdering();
        $this->applyLimit();

        return $this->envelopes;
    }

    public function getEnvelopes(): array
    {
        return $this->envelopes;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function addSpecification(SpecificationInterface $specification): void
    {
        $this->specification = null !== $this->specification
            ? new AndSpecification($this->specification, $specification)
            : $specification;
    }

    public function orderBy(array $orderings): void
    {
        $this->orderings = $orderings;
    }

    public function setMaximumResults(int $maximumResults): void
    {
        $this->maximumResults = $maximumResults;
    }

    private function applySpecification(): void
    {
        if (null === $this->specification) {
            return;
        }

        $specification = $this->specification;

        $this->envelopes = array_filter($this->envelopes, function (NotificationInterface $notification) use ($specification) {
            return $specification->isSatisfiedBy($notification);
        });
    }

    private function applyOrdering(): void
    {
        if (null === $this->orderings) {
            return;
        }

        $orderings = $this->orderings;

        usort($this->envelopes, function (NotificationInterface $first, NotificationInterface $second) use ($orderings) {
            foreach ($orderings as $field => $ordering) {
                $stampA = $first->last($field);
                $stampB = $second->last($field);

                if (!$stampA instanceof OrderableStampInterface || !$stampB instanceof OrderableStampInterface) {
                    return 0;
                }

                if (Filter::ASC === $ordering) {
                    return $stampA->compare($stampB);
                }

                return $stampB->compare($stampA);
            }

            return 0;
        });
    }

    private function applyLimit(): void
    {
        if (null === $this->maximumResults) {
            return;
        }

        $this->envelopes = \array_slice($this->envelopes, 0, $this->maximumResults, true);
    }

}