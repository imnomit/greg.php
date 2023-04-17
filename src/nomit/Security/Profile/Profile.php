<?php

namespace nomit\Security\Profile;

use nomit\Security\Profile\Repository\RepositoryInterface;
use nomit\Utility\Concern\Serializable;

abstract class Profile implements ProfileInterface
{

    private array $repositories = [];

    public function __construct(
        array $repositories = []
    )
    {
        if(!empty($repositories)) {
            $this->setRepositories($repositories);
        }
    }

    public function initialize(int $userId): self
    {
        foreach($this->repositories as $repository) {
            $repository->initialize($userId);
        }

        return $this;
    }

    public function setRepositories(array $repositories): self
    {
        $this->repositories = [];

        foreach($repositories as $name => $repository) {
            $this->setRepository($name, $repository);
        }

        return $this;
    }

    public function setRepository(string $name, RepositoryInterface $repository): self
    {
        $this->repositories[$name] = $repository;

        return $this;
    }

    public function hasRepository(string $name): bool
    {
        return isset($this->repositories[$name]);
    }

    public function getRepository(string $name): ?RepositoryInterface
    {
        return $this->repositories[$name] ?? null;
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function removeRepository(string $name): void
    {
        unset($this->repositories[$name]);
    }

    public function toArray(): array
    {
        $repositories = [];

        foreach($this->repositories as $name => $repository) {
            $repositories[$name] = $repository->toArray();
        }

        return $repositories;
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | $options);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toJson();
    }

    public function __serialize(): array
    {
        return [$this->repositories];
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        [$this->repositories] = $data;
    }

    public function unserialize(string $payload): ?self
    {
        if(!($result = @unserialize($payload))) {
            return null;
        }

        $this->__unserialize($result);

        return $this;
    }

}