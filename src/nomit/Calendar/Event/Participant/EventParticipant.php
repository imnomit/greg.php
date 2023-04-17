<?php

namespace nomit\Calendar\Event\Participant;

use nomit\Calendar\Event\Participant\Invitation\EventInvitation;
use nomit\Calendar\Event\Participant\Invitation\EventInvitationInterface;
use nomit\Calendar\Event\Participant\Invitation\EventInvitationResponse;
use nomit\Security\User\UserInterface;
use nomit\Utility\Concern\Serializable;

class EventParticipant implements EventParticipantInterface
{

    private EventParticipantRole $role;

    public static function fromInvitation(EventInvitationInterface $invitation): EventParticipantInterface
    {
        return new self($invitation);
    }

    public function __construct(
        private EventInvitationInterface $invitation,
        EventParticipantRole $role = null
    )
    {
        if(!$role) {
            $role = new EventParticipantRole();
        }

        $this->role = $role;
    }

    /**
     * @param EventInvitationInterface $invitation
     */
    public function setInvitation(EventInvitationInterface $invitation): self
    {
        $this->invitation = $invitation;

        return $this;
    }

    /**
     * @return EventInvitationInterface
     */
    public function getInvitation(): EventInvitationInterface
    {
        return $this->invitation;
    }

    public function getOriginUser(): UserInterface
    {
        return $this->invitation->getOriginUser();
    }

    public function getDestinationUser(): UserInterface
    {
        return $this->invitation->getDestinationUser();
    }

    public function getInvitationDateTime(): \DateTimeInterface
    {
        return $this->invitation->getDateTime();
    }

    public function getInvitationResponse(): EventInvitationResponse
    {
        return $this->invitation->getResponse();
    }

    public function isInvitationSent(): bool
    {
        return $this->invitation->isSent();
    }

    public function isInvitationAccepted(): bool
    {
        return $this->invitation->isAccepted();
    }

    public function isInvitationTentative(): bool
    {
        return $this->invitation->isTentative();
    }

    public function isInvitationDeclined(): bool
    {
        return $this->invitation->isDeclined();
    }

    public function getInvitationResponseDateTime(): ?\DateTimeInterface
    {
        return $this->invitation->getResponseDateTime();
    }

    /**
     * @param EventParticipantRole|int $role
     */
    public function setRole(EventParticipantRole|int $role): self
    {
        if(is_int($role)) {
            $role = new EventParticipantRole($role);
        }

        $this->role = $role;

        return $this;
    }

    /**
     * @return EventParticipantRole
     */
    public function getRole(): EventParticipantRole
    {
        return $this->role;
    }

    public function isParticipant(): bool
    {
        return $this->role->isParticipant();
    }

    public function isManager(): bool
    {
        return $this->role->isManager();
    }

    public function addRole(int|EventParticipantRole $role): EventParticipantInterface
    {
        if($role instanceof EventParticipantRole) {
            $role = $role->getRole();
        }

        $this->role->addRole($role);

        return $this;
    }

    public function removeRole(int|EventParticipantRole $role): EventParticipantInterface
    {
        if($role instanceof EventParticipantRole) {
            $role = $role->getRole();
        }

        $this->role->removeRole($role);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'invitation' => $this->getInvitation()->toArray(),
            'role' => $this->getRole()->getRole(),
        ];
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __serialize(): array
    {
        return [
            $this->getInvitation(),
            $this->getRole()
        ];
    }

    public function unserialize(string $payload): ?self
    {
        $this->__unserialize(unserialize($payload));

        return $this;
    }

    public function __unserialize(array $data): void
    {
        [
            $this->invitation,
            $this->role
        ] = $data;
    }

}