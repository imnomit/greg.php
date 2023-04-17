<?php

namespace nomit\Calendar\Event\Participant;

use nomit\Calendar\Exception\InvalidArgumentException;

final class EventParticipantRole implements \nomit\Utility\Concern\Arrayable
{

    public const ROLE_PARTICIPANT = 0b01;

    public const ROLE_MANAGER = 0b10;

    private int $role;

    public static function is(int $subject, int $role): bool
    {
        return $subject & $role;
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_PARTICIPANT,
            self::ROLE_MANAGER
        ];
    }

    public function __construct(
        int $role = null
    )
    {
        if($role === null) {
            $role = self::ROLE_PARTICIPANT;
        }

        $this->setRole($role);
    }

    /**
     * @param int $role
     */
    public function setRole(int $role): self
    {
        if(!in_array($role, ($roles = $this->toArray()))) {
            throw new InvalidArgumentException(sprintf('The supplied event participant role, "%s", is invalid. The supported participant roles are: "%s".', $role, implode(',', $roles)));
        }

        $this->role = $role;

        return $this;
    }

    public function addRole(int $role): self
    {
        if(!self::is($this->role, $role)) {
            $this->role &= $role;
        }

        return $this;
    }

    public function removeRole(int $role): self
    {
        if(self::is($this->role, $role)) {
            $this->role &= ~$role;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    public function isParticipant(): bool
    {
        return self::is($this->role, self::ROLE_PARTICIPANT);
    }

    public function isManager(): bool
    {
        return self::is($this->role, self::ROLE_MANAGER);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return self::getRoles();
    }

    /**
     * @inheritDoc
     */
    public function __toArray(): array
    {
        return $this->toArray();
    }

}