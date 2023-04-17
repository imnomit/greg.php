<?php

namespace nomit\Toasting\Filter;

use nomit\Toasting\Filter\Specification\SpecificationInterface;

interface FilterInterface
{

    public const ASC = 'ASC';

    public const DESC = 'DESC';

    public function getResult(): array;

    public function getEnvelopes(): array;

    public function getCriteria(): array;

    public function addSpecification(SpecificationInterface $specification): void;

    public function orderBy(array $orderings): void;

    public function setMaximumResults(int $maximumResults): void;


}