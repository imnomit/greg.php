<?php

namespace nomit\Process\Model;

interface ModelInterface
{

    public function getId(): int;

    public function getParentId(): int;

    public function setUserId(int $userId): self;

    public function getUserId(): int;

    public function setUserName(string $username): self;

    public function getUserName(): string;

    public function setGroupId(int $groupId): self;

    public function getGroupId(): int;

    public function setGroupName(string $name): self;

    public function getGroupName(): string;

    public function detachSession(): int;

    public function getSessionId(): int;


}