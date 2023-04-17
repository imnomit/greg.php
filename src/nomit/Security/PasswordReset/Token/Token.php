<?php

namespace nomit\Security\PasswordReset\Token;

use nomit\Utility\Concern\Serializable;

final class Token implements TokenInterface
{

    public function __construct(
        private string $token,
        private \DateTimeInterface $expiryDateTime,
        private int $userId
    )
    {
    }

    public function setToken(string $token): TokenInterface
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setExpiryDateTime(\DateTimeInterface $dateTime): TokenInterface
    {
        $this->expiryDateTime = $dateTime;

        return $this;
    }

    public function getExpiryDateTime(): \DateTimeInterface
    {
        return $this->expiryDateTime;
    }

    public function setUserId(int $userId): TokenInterface
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function toString(): string
    {
        return $this->getToken();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toArray(): array
    {
        return [
            'token' => $this->getToken(),
            'expiry_datetime' => $this->getExpiryDateTime(),
            'user_id' => $this->getUserId()
        ];
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function __serialize(): array
    {
        return [$this->token, $this->expiryDateTime, $this->userId];
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        [$this->token, $this->expiryDateTime, $this->userId] = $data;
    }

    public function unserialize(string $payload): ?self
    {
        $this->__unserialize(unserialize($payload));

        return $this;
    }

}