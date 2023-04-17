<?php

namespace nomit\Toasting\Toast;

use nomit\Toasting\Toast\Property\PropertyInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Jsonable;
use nomit\Utility\Concern\Serializable;

interface ToastInterface extends Arrayable, Serializable
{

    public const LEVELS = [
        self::LEVEL_ERROR,
        self::LEVEL_WARNING,
        self::LEVEL_NOTICE,
        self::LEVEL_DEBUG,
        self::LEVEL_SUCCESS
    ];

    public const LEVEL_ERROR = -2;

    public const LEVEL_WARNING = -1;

    public const LEVEL_NOTICE = 0;

    public const LEVEL_DEBUG = 1;

    public const LEVEL_SUCCESS = 2;

    public static function fromArray(array $payload): self;

    public function setLevel(int $level): self;

    public function getLevel(): int;

    public function setTitle(string $title): self;

    public function getTitle(): string;

    public function setMessage(string $message): self;

    public function getMessage(): ?string;

    public function getParsedMessage(): string;

    public function setDateTime(\DateTimeInterface $dateTime): self;

    public function getDateTime(): \DateTimeInterface;

    public function read(bool $read = true): self;

    public function isRead(): bool;

    public function addProperties(array $properties): self;

    public function addProperty(PropertyInterface $property): self;

    public function hasProperty(string $name): bool;

    public function getProperty(string $name): ?PropertyInterface;

    public function removeProperty(string $name): void;

    public function getProperties(): array;

    public function setOptions(array $options): self;

    public function setOption(string $name, mixed $value): self;

    public function hasOption(string $name): bool;

    public function getOption(string $name): mixed;

    public function getOptions(): array;

}