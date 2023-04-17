<?php

namespace Application\Summary\Administration;

use nomit\Utility\Repository\Repository;
use nomit\Utility\Repository\RepositoryInterface;

abstract class AbstractSummaryExtension implements SummaryExtensionInterface
{

    private RepositoryInterface $repository;

    public function __construct(array $items = [])
    {
        $this->repository = new Repository($items);
    }

    public function set(string $name, mixed $value): SummaryExtensionInterface
    {
        $this->repository->set($name, $value);

        return $this;
    }

    public function has(string $name): bool
    {
        return $this->repository->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->repository->get($name, $default);
    }

    public function remove(string $name): void
    {
        $this->repository->remove($name);
    }

    public function all(array $items = null): array|self
    {
        if($items === null) {
            return $this->repository->all();
        }

        $this->repository->setAll($items);

        return $this;
    }

    public function toArray(): array
    {
        return $this->repository->toArray();
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}