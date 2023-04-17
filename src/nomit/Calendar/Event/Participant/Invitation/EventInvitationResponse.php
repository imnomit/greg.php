<?php

namespace nomit\Calendar\Event\Participant\Invitation;

use nomit\Calendar\Exception\InvalidArgumentException;

final class EventInvitationResponse implements \nomit\Utility\Concern\Arrayable
{

    public const RESPONSE_SENT = 0;

    public const RESPONSE_ACCEPTED = 1;

    public const RESPONSE_TENTATIVE = 2;

    public const RESPONSE_DECLINED = -1;

    private int $response;

    public static function is(int $subject, int $response): bool
    {
        return $subject === $response;
    }

    public static function getResponses(): array
    {
        return [
            self::RESPONSE_SENT,
            self::RESPONSE_ACCEPTED,
            self::RESPONSE_TENTATIVE,
            self::RESPONSE_DECLINED
        ];
    }

    public function __construct(
        int $response = null
    )
    {
        if($response === null) {
            $response = self::RESPONSE_SENT;
        }

        $this->response = $response;
    }

    public function setResponse(int $response): self
    {
        if(!in_array($response, ($responses = $this->toArray()))) {
            throw new InvalidArgumentException(sprintf('The supplied event invitation response, "%s", is invalid. The supported invitation responses are: "%s".', $response, implode(',', $responses)));
        }

        $this->response = $response;

        return $this;
    }

    /**
     * @return int
     */
    public function getResponse(): int
    {
        return $this->response;
    }

    public function isSent(): bool
    {
        return self::is($this->response, self::RESPONSE_SENT);
    }

    public function isAccepted(): bool
    {
        return self::is($this->response, self::RESPONSE_ACCEPTED);
    }

    public function isTentative(): bool
    {
        return self::is($this->response, self::RESPONSE_TENTATIVE);
    }

    public function isDeclined(): bool
    {
        return self::is($this->response, self::RESPONSE_DECLINED);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return self::getResponses();
    }

    /**
     * @inheritDoc
     */
    public function __toArray(): array
    {
        return $this->toArray();
    }

}