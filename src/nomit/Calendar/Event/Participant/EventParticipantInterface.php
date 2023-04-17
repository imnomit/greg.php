<?php

namespace nomit\Calendar\Event\Participant;

use nomit\Calendar\Event\EventInterface;
use nomit\Calendar\Event\Participant\Invitation\EventInvitationInterface;
use nomit\Calendar\Event\Participant\Invitation\EventInvitationResponse;
use nomit\Security\User\UserInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Serializable;

interface EventParticipantInterface extends Arrayable, Serializable
{

    public static function fromInvitation(EventInvitationInterface $invitation): self;

    public function setInvitation(EventInvitationInterface $invitation): self;

    public function getInvitation(): EventInvitationInterface;

    public function getOriginUser(): UserInterface;

    public function getDestinationUser(): UserInterface;

    public function getInvitationDateTime(): \DateTimeInterface;

    public function getInvitationResponse(): EventInvitationResponse;

    public function isInvitationSent(): bool;

    public function isInvitationAccepted(): bool;

    public function isInvitationTentative(): bool;

    public function isInvitationDeclined(): bool;

    public function getInvitationResponseDateTime(): ?\DateTimeInterface;

    public function setRole(EventParticipantRole|int $role): self;

    public function getRole(): EventParticipantRole;

    public function isParticipant(): bool;

    public function isManager(): bool;

    public function addRole(int|EventParticipantRole $role): self;

    public function removeRole(int|EventParticipantRole $role): self;

}