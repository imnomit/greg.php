<?php

namespace nomit\Error\Solution\SolutionProvider;

use nomit\Error\Solution\Solution;
use nomit\Error\Solution\Solution\ThrowableSolutionInterface;
use nomit\Utility\String\Strings;
use Throwable;
use UnexpectedValueException;

class InvalidRouteActionSolutionProvider implements ThrowableSolutionInterface
{

    protected const REGEX = '/\[([a-zA-Z\\\\]+)\]/m';

    public function canSolve(Throwable $throwable): bool
    {
        if (!$throwable instanceof UnexpectedValueException) {
            return false;
        }

        if (!preg_match(self::REGEX, $throwable->getMessage(), $matches)) {
            return false;
        }

        return Strings::startsWith($throwable->getMessage(), 'Invalid route action: ');
    }

    public function getSolutions(Throwable $throwable): array
    {
        preg_match(self::REGEX, $throwable->getMessage(), $matches);

        $invalidController = $matches[1] ?? null;

        return [
            Solution\Solution::create("`{$invalidController}` was not found.")
                ->setSolutionDescription("Controller class `{$invalidController}` for one of your Routes was not found. Are you sure this controller exists and is imported correctly?"),
        ];
    }

}