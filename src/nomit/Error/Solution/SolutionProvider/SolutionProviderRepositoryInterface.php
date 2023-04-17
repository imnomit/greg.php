<?php

namespace nomit\Error\Solution\SolutionProvider;

use nomit\Error\Solution\Solution\SolutionInterface;
use Throwable;

interface SolutionProviderRepositoryInterface
{

    /**
     * @param string $solutionProviderClass
     * @return $this
     */
    public function registerSolutionProvider(string $solutionProviderClass): self;

    /**
     * @param array $solutionProviderClasses
     * @return $this
     */
    public function registerSolutionProviders(array $solutionProviderClasses): self;

    /**
     * @param Throwable $throwable
     * @return SolutionInterface[]
     */
    public function getSolutionsForThrowable(Throwable $throwable): array;

    /**
     * @param string $solutionClass
     * @return SolutionInterface|null
     */
    public function getSolutionForClass(string $solutionClass): ?SolutionInterface;

}