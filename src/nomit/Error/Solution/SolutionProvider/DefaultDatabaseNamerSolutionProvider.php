<?php

namespace nomit\Error\Solution\SolutionProvider;

use nomit\Database\Exception\DatabaseException;
use nomit\Error\Solution\Solution\ThrowableSolutionInterface;
use nomit\Error\Solution\Solutions\SuggestUsingCorrectDatabaseNameSolution;
use Throwable;

class DefaultDatabaseNamerSolutionProvider implements ThrowableSolutionInterface
{

    const MYSQL_UNKNOWN_DATABASE_CODE = 1049;

    public function canSolve(Throwable $throwable): bool
    {
        if(!$throwable instanceof DatabaseException && $throwable->getCode() !== self::MYSQL_UNKNOWN_DATABASE_CODE) {
            return false;
        }

        return true;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new SuggestUsingCorrectDatabaseNameSolution()];
    }
}