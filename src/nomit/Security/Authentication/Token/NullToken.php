<?php

namespace nomit\Security\Authentication\Token;

use nomit\Exception\BadMethodCallException;
use nomit\Security\User\UserInterface;

class NullToken implements TokenInterface
{

    public function isAuthenticated(): bool
    {
        return false;
    }

    public function setAuthenticated(bool $authenticated): TokenInterface
    {
        throw new \BadMethodCallException(sprintf('You cannot alter the "authenticated" attribute on "%s".', __CLASS__));
    }

    public function getRoles(): array
    {
        return [];
    }

    public function getRoleNames(): array
    {
        return [];
    }

    public function getUser(): ?UserInterface
    {
        return null;
    }

    public function setUser(UserInterface $user): TokenInterface
    {
        throw new BadMethodCallException(sprintf('The user property cannot be set on the "%s" class.', __CLASS__));
    }

    public function getUserId(): int
    {
        return 0;
    }

    public function getUsername(): string
    {
        return '';
    }

    public function getEmail(): string
    {
        return '';
    }

    public function eraseCredentials(): void
    {
    }

    public function setAll(array $data): TokenInterface
    {
        throw new BadMethodCallException(sprintf('Attributes can not be set on the %s" class.', __CLASS__));
    }

    public function getAll(): array
    {
        return [];
    }

    public function set(string $name, mixed $value): TokenInterface
    {
        throw new BadMethodCallException(sprintf('Attributes cannot be set on the "%s" class.', __CLASS__));
    }

    public function has(string $name): bool
    {
        return false;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return null;
    }

    public function authenticate(bool $authenticate = true): TokenInterface
    {
        return $this;
    }

    public function all(): array
    {
        return [];
    }

    public function remove(string $name): void
    {
    }

    public function toString(): string
    {
        return '';
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function fromString(string $payload): TokenInterface
    {
        return $this;
    }

    public function toArray(): array
    {
        return [];
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function toJson(int $options = 0): string
    {
        return '';
    }

    public function jsonSerialize(): mixed
    {
        return $this->toJson();
    }

    public function isEqualTo(TokenInterface $token): bool
    {
        return $token instanceof NullToken;
    }

}