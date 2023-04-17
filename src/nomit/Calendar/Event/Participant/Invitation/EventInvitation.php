<?php

namespace nomit\Calendar\Event\Participant\Invitation;

use nomit\Calendar\Event\EventInterface;
use nomit\Calendar\Exception\InvalidArgumentException;
use nomit\Security\User\UserInterface;
use nomit\Utility\Concern\Serializable;
final class EventInvitation implements EventInvitationInterface
{

    private UserInterface $destinationUser;

    private \DateTimeInterface $dateTime;

    private EventInvitationResponse $response;

    private ?\DateTimeInterface $responseDateTime = null;
    
    public function __construct(
        private EventInterface  $event,
        private UserInterface   $originUser,
        UserInterface           $destinationUser,
        \DateTimeInterface      $dateTime = null,
        EventInvitationResponse $response = null,
        \DateTimeInterface      $responseDateTime = null
    )
    {
        $this->setDestinationUser($destinationUser);

        if(!$dateTime) {
            $dateTime = new \DateTime('NOW');
        }

        if(!$response) {
            $response = new EventInvitationResponse();
        }

        if(!$responseDateTime) {
            $responseDateTime = new \DateTime('NOW');
        }

        $this->dateTime = $dateTime;
        $this->response = $response;
        $this->responseDateTime = $responseDateTime;
    }

    public function setEvent(EventInterface $event): EventInvitationInterface
    {
        $this->event = $event;

        return $this;
    }

    public function getEvent(): EventInterface
    {
        return $this->event;
    }

    public function setOriginUser(UserInterface $user): EventInvitationInterface
    {
        $this->originUser = $user;

        return $this;
    }

    public function getOriginUser(): UserInterface
    {
        return $this->originUser;
    }

    public function setDestinationUser(UserInterface $user): EventInvitationInterface
    {
        if(($originUserId = $this->originUser->getUserId()) === ($destinationUserId = $user->getUserId())) {
            throw new InvalidArgumentException(sprintf('The supplied origin, user ID "%s", and destination, user ID "%s", event invitation users are the same: event invitations cannot be addressed to the sender.', $originUserId, $destinationUserId));
        }

        $this->destinationUser = $user;

        return $this;
    }

    public function getDestinationUser(): UserInterface
    {
        return $this->destinationUser;
    }

    public function setDateTime(\DateTimeInterface $dateTime): EventInvitationInterface
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setResponse(EventInvitationResponse $response): EventInvitationInterface
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): EventInvitationResponse
    {
        return $this->response;
    }

    public function isSent(): bool
    {
        return $this->response->isSent();
    }

    public function isAccepted(): bool
    {
        return $this->response->isAccepted();
    }

    public function isTentative(): bool
    {
        return $this->response->isTentative();
    }

    public function isDeclined(): bool
    {
        return $this->response->isDeclined();
    }

    public function setResponseDateTime(\DateTimeInterface $dateTime): EventInvitationInterface
    {
        if($dateTime < $this->responseDateTime) {
            throw new InvalidArgumentException(sprintf('The supplied event invitation response date time, "%s", is less than the date time at which the invitation was sent, "%s": the invitation response date time must be greater than, that is, after, the invitation sending date time.', $dateTime->format('c'), $this->dateTime->format('c')));
        }

        $this->responseDateTime = $dateTime;

        return $this;
    }

    public function getResponseDateTime(): ?\DateTimeInterface
    {
        return $this->responseDateTime;
    }

    public function toArray(): array
    {
        return [
            'event' => $this->event->toArray(),
            'origin_user' => $this->originUser->toArray(),
            'destination_user' => $this->destinationUser->toArray(),
            'datetime' => $this->dateTime,
            'response' => $this->response->getResponse(),
            'response_datetime' => $this->responseDateTime
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
            $this->getEvent(),
            $this->getOriginUser(),
            $this->getDestinationUser(),
            $this->getDateTime(),
            $this->getResponse(),
            $this->getResponseDateTime()
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
            $this->event,
            $this->originUser,
            $this->destinationUser,
            $this->dateTime,
            $this->response,
            $this->responseDateTime
        ] = $data;
    }

}