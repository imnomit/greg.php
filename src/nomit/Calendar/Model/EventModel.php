<?php

namespace nomit\Calendar\Model;

use nomit\Calendar\Event\Event;
use nomit\Calendar\Event\EventInterface;
use nomit\Calendar\Exception\InvalidArgumentException;
use nomit\Security\User\UserInterface;
use nomit\Utility\Concern\Serializable;

class EventModel extends AbstractModel
{

    protected ?EventInterface $event = null;

    protected int $calendarId;

    private $calendarProvider;

    protected ?ModelInterface $calendar = null;

    protected int $userId;

    private $userProvider;

    protected ?UserInterface $user = null;

    protected string $location;

    protected string $title;

    protected ?string $description = null;

    protected bool $allDay = false;

    protected \DateTimeInterface $creationDateTime;

    protected ?\DateTimeInterface $updateDateTime = null;

    protected ?string $url;

    protected ?string $emoji;

    protected ?string $banner;

    public static function fromEvent(EventInterface $event): self
    {
        $instance = new self($event->getId());

        return $instance->setEvent($event);
    }

    public static function fromArray(array $data): ModelInterface
    {
        if(!isset($data['id'])) {
            throw new InvalidArgumentException('The event represented by the supplied array payload is missing the expected ID property.');
        }

        if(!isset($data['start_datetime'], $data['end_datetime']) && (!isset($data['all_day']) || !$data['all_day'])) {
            throw new InvalidArgumentException('The event represented by the supplied array payload is not flagged as an "all day" event, and thus is expected to have set both "start_datetime" and "end_datetime" values.');
        }

        $instance = new self($data['id']);

        if(!isset($data['all_day']) || !$data['all_day']) {
            $instance->setEvent(new Event($data['id'], new \DateTime($data['start_datetime']), new \DateTime($data['end_datetime'])));
        }

        if(isset($data['calendar_id'])) {
            $instance->setCalendarId($data['calendar_id']);
        }

        if(isset($data['user_id'])) {
            $instance->setUserId($data['user_id']);
        }

        if(isset($data['location'])) {
            $instance->setLocation($data['location']);
        }

        if(isset($data['title'])) {
            $instance->setTitle($data['title']);
        }

        if(isset($data['all_day'])) {
            $data['all_day'] > 0
                ? $instance->enableAllDay()
                : $instance->disableAllDay();
        }

        if(isset($data['creation_datetime'])) {
            $instance->setCreationDateTime($data['creation_datetime']);
        }

        if(isset($data['update_datetime'])) {
            $instance->setUpdateDateTime($data['update_datetime']);
        }

        if(isset($data['url'])) {
            $instance->setUrl($data['url']);
        }

        if(isset($data['emoji'])) {
            $instance->setEmoji($data['emoji']);
        }

        if(isset($data['banner'])) {
            $instance->setBanner($data['banner']);
        }

        return $instance;
    }

    public function __construct(
        protected int $id
    )
    {
    }

    public function setEvent(EventInterface $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getEvent(): ?EventInterface
    {
        return $this->event;
    }

    public function setCalendarId(int $calendarId): self
    {
        $this->calendarId = $calendarId;

        if($this->calendarProvider) {
            $this->calendar = ($this->calendarProvider)($this->calendarId);
        }

        return $this;
    }

    public function getCalendarId(): int
    {
        return $this->calendarId;
    }

    public function setCalendarProvider(callable $provider): self
    {
        $this->calendarProvider = $provider;

        return $this;
    }

    public function getCalendar(): ?ModelInterface
    {
        return $this->calendar;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        if($this->userProvider) {
            $this->user = ($this->userProvider)($this->userId);
        }

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserProvider(callable $provider): self
    {
        $this->userProvider = $provider;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function enableAllDay(): self
    {
        $this->allDay = true;

        return $this;
    }

    public function disableAllDay(): self
    {
        $this->allDay = false;

        return $this;
    }

    public function isAllDay(): bool
    {
        return $this->allDay;
    }

    public function setCreationDateTime(\DateTimeInterface $dateTime): self
    {
        $this->creationDateTime = $dateTime;

        return $this;
    }

    public function getCreationDateTime(): \DateTimeInterface
    {
        return $this->creationDateTime;
    }

    public function setUpdateDateTime(?\DateTimeInterface $dateTime): self
    {
        $this->updateDateTime = $dateTime;

        return $this;
    }

    public function getUpdateDateTime(): ?\DateTimeInterface
    {
        return $this->updateDateTime;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $emoji
     */
    public function setEmoji(?string $emoji): self
    {
        $this->emoji = $emoji;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    /**
     * @param string|null $banner
     */
    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBanner(): ?string
    {
        return $this->banner;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'calendar_id' => $this->getCalendarId(),
            'user_id' => $this->getUserId(),
            'location' => $this->getLocation(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'all_day' => $this->isAllDay(),
            'start_datetime' => $this->getEvent()->getStart(),
            'end_datetime' => $this->getEvent()->getEnd(),
            'creation_datetime' => $this->getCreationDateTime(),
            'update_datetime' => $this->getUpdateDateTime()
        ];
    }

    public function __serialize(): array
    {
        return [
            $this->getId(),
            $this->getEvent(),
            $this->getCalendarId(),
            $this->getCalendar(),
            $this->getUserId(),
            $this->getUser(),
            $this->getLocation(),
            $this->getTitle(),
            $this->getDescription(),
            $this->isAllDay(),
            $this->getCreationDateTime(),
            $this->getUpdateDateTime()
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->id,
            $this->event,
            $this->calendarId,
            $this->calendar,
            $this->userId,
            $this->user,
            $this->location,
            $this->title,
            $this->description,
            $this->allDay,
            $this->creationDateTime,
            $this->updateDateTime
        ] = $data;
    }

}