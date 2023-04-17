<?php

namespace nomit\Security\Authentication\Token;

use nomit\Security\User\UserInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Jsonable;
use nomit\Utility\Concern\Serializable;

interface TokenInterface extends Arrayable, Jsonable
{

    public function setUser(UserInterface $user): self;

    public function getUser(): ?UserInterface;

    public function getUserId(): ?int;

    public function getUsername(): ?string;

    public function getEmail(): ?string;

    public function authenticate(bool $authenticate = true): self;

    public function isAuthenticated(): bool;

    public function eraseCredentials(): void;

    public function getRoles(): array;

    public function getRoleNames(): array;

    public function setAll(array $data): self;

    public function all(): array;

    public function set(string $name, mixed $value): self;

    public function has(string $name): bool;

    public function get(string $name, mixed $default = null): mixed;

    public function remove(string $name): void;

    public function isEqualTo(TokenInterface $token): bool;

}