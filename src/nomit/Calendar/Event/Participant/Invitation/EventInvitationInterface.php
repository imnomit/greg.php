<?php

namespace nomit\Calendar\Event\Participant\Invitation;

use nomit\Calendar\Event\EventInterface;
use nomit\Calendar\Event\Participant\EventParticipantInterface;
use nomit\Security\User\UserInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Serializable;

interface EventInvitationInterface extends Arrayable, Serializable
{

    public function setEvent(EventInterface $event): self;

    public function getEvent(): EventInterface;

    public function setOriginUser(UserInterface $user): self;

    public function getOriginUser(): UserInterface;

    public function setDestinationUser(UserInterface $user): self;

    public function getDestinationUser(): UserInterface;

    public function setDateTime(\DateTimeInterface $dateTime): self;

    public function getDateTime(): \DateTimeInterface;

    public function setResponse(EventInvitationResponse $response): self;

    public function getResponse(): EventInvitationResponse;

    public function isSent(): bool;

    public function isAccepted(): bool;

    public function isTentative(): bool;

    public function isDeclined(): bool;

    public function setResponseDateTime(\DateTimeInterface $dateTime): self;

    public function getResponseDateTime(): ?\DateTimeInterface;

}