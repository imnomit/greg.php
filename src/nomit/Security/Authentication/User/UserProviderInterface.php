<?php

namespace nomit\Security\Authentication\User;

use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Security\User\Authentication\Validation\BanInterface;
use nomit\Security\User\UserInterface;

interface UserProviderInterface
{

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): self;

    public function getEventDispatcher(): ?EventDispatcherInterface;

    public function getByUsername(string $username): ?UserInterface;

    public function getByEmail(string $email): ?UserInterface;

    public function getByUserId(int $userId): ?UserInterface;

    public function refresh(UserInterface $user): ?UserInterface;

    public function supports(string $className): bool;

    public function verify(int $userId): bool;

    public function all(int $index = 0, int $limit = null): array;

    public function save(UserInterface $user): int|bool;

    public function ban(BanInterface $ban): bool;

    public function unban(UserInterface $user): bool;

}