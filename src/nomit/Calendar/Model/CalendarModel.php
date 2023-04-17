<?php

namespace nomit\Calendar\Model;

use nomit\Calendar\Exception\InvalidArgumentException;

class CalendarModel extends AbstractModel
{

    protected ?int $parentCalendarId = null;

    private $parentCalendarProvider;

    protected ?ModelInterface $parentCalendar = null;

    protected string $title;

    protected ?string $description = null;

    protected \DateTimeInterface $creationDateTime;

    protected ?string $color = null;

    protected ?string $emoji = null;

    public static function fromArray(array $data): ModelInterface
    {
        if(!isset($data['id'])) {
            throw new InvalidArgumentException('The calendar represented by the supplied array payload is missing the expected ID property.');
        }

        $instance = new self($data['id']);

        if(isset($data['parent_id'])) {
            $instance->setParentCalendarId($data['parentId']);
        }

        if(isset($data['title'])) {
            $instance->setTitle($data['title']);
        }

        if(isset($data['description'])) {
            $instance->setDescription($data['description']);
        }

        if(isset($data['user_id'])) {
            $instance->setUserId($data['user_id']);
        }

        if(isset($data['creation_datetime'])) {
            $instance->setCreationDateTime($data['creation_datetime']);
        }

        if(isset($data['color'])) {
            $instance->setColor($data['color']);
        }

        if(isset($data['emoji'])) {
            $instance->setEmoji($data['emoji']);
        }

        return $instance;
    }

    public function __construct(
        protected int $id
    )
    {
    }

    /**
     * @param int|null $parentId
     */
    public function setParentCalendarId(?int $parentId): self
    {
        $this->parentCalendarId = $parentId;

        if($this->parentCalendarProvider) {
            $this->parentCalendar = ($this->parentCalendarProvider)($this->parentCalendarId);
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getParentCalendarId(): ?int
    {
        return $this->parentCalendarId;
    }

    public function setParentCalendarProvider(callable $provider): self
    {
        $this->parentCalendarProvider = $provider;

        return $this;
    }

    /**
     * @return ModelInterface|null
     */
    public function getParentCalendar(): ?ModelInterface
    {
        return $this->parentCalendar;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param \DateTimeInterface $creationDateTime
     */
    public function setCreationDateTime(\DateTimeInterface $creationDateTime): self
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreationDateTime(): \DateTimeInterface
    {
        return $this->creationDateTime;
    }

    /**
     * @param string|null $color
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
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
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'parent_id' => $this->getParentCalendarId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'creation_datetime' => $this->getCreationDateTime(),
            'color' => $this->getColor(),
            'emoji' => $this->getEmoji()
        ];
    }

    public function __serialize(): array
    {
        return [
            $this->getId(),
            $this->getParentCalendarId(),
            $this->getParentCalendar(),
            $this->getTitle(),
            $this->getDescription(),
            $this->getCreationDateTime(),
            $this->getColor(),
            $this->getEmoji()
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->id,
            $this->parentCalendarId,
            $this->parentCalendar,
            $this->title,
            $this->description,
            $this->creationDateTime,
            $this->color,
            $this->emoji
        ] = $data;
    }

}