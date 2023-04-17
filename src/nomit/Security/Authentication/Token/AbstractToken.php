<?php

namespace nomit\Security\Authentication\Token;

use nomit\Dumper\Dumper;
use nomit\Security\Authorization\Role\RoleInterface;
use nomit\Security\User\UserInterface;
use nomit\Utility\Repository\Repository;
use nomit\Utility\Repository\RepositoryInterface;

abstract class AbstractToken implements TokenInterface
{

    private bool $authenticated = false;

    private ?RepositoryInterface $repository = null;

    public function __construct(UserInterface $user = null)
    {
        $this->repository = new Repository();

        $this->repository?->set('user', $user);
    }

    public function setUser(UserInterface $user): TokenInterface
    {
        $this->repository?->set('user', $user);

        return $this;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->repository?->get('user');
    }

    public function getUserId(): ?int
    {
        return $this->getUser()?->getUserId();
    }

    public function getUsername(): ?string
    {
        return $this->getUser()?->getUsername();
    }

    public function getEmail(): ?string
    {
        return $this->getUser()?->getEmail();
    }

    public function authenticate(bool $authenticate = true): TokenInterface
    {
        $this->authenticated = $authenticate;

        return $this;
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function eraseCredentials(): void
    {
        $this->getUser()?->eraseCredentials();
    }

    public function getRoles(): array
    {
        return $this->getUser()?->getRoles() ?? [];
    }

    public function getRoleNames(): array
    {
        return array_map(function(RoleInterface $role) {
            return $role->getName();
        }, $this->getRoles());
    }

    public function setAll(array $data): TokenInterface
    {
        $this->repository?->setAll($data);

        return $this;
    }

    public function all(): array
    {
        return $this->repository?->all();
    }

    public function set(string $name, mixed $value): TokenInterface
    {
        $this->repository?->set($name, $value);

        return $this;
    }

    public function has(string $name): bool
    {
        return $this->repository?->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->repository?->get($name, $default);
    }

    public function remove(string $name): void
    {
        $this->repository?->remove($name);
    }

    public function isEqualTo(TokenInterface $token): bool
    {
        return spl_object_hash($this) === spl_object_hash($token);
    }

    public function toArray(): array
    {
        return [
            'user' => $this->getUser()?->toArray() ?? [],
            'authenticated' => $this->isAuthenticated(),
            'data' => $this->repository?->toArray()
        ];
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
        return [$this->authenticated, $this->repository?->toArray()];
    }

    public function __unserialize(array $payload): void
    {
        [$this->authenticated, $repository] = $payload;

        if(is_array($repository)) {
            $this->repository = new Repository($repository);
        }
    }

}