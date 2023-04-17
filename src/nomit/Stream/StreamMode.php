<?php

namespace nomit\Stream;

use nomit\Utility\Concern\Stringable;

final class StreamMode implements StreamModeInterface
{

    private string $mode;

    private string $base;

    private string $plus;

    private string $flag;

    public function __construct(
        string|Stringable|StreamModeInterface $mode
    )
    {
        $this->mode = $mode = (string) $mode;

        $mode = substr($mode, 0, 3);
        $rest = substr($mode, 1);

        $this->base = substr($mode, 0, 1);
        $this->plus = str_contains($rest, '+');
        $this->flag = trim($rest, '+');
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    public function canRead(): bool
    {
        if($this->plus) {
            return true;
        }

        return $this->base === 'r';
    }

    public function canWrite(): bool
    {
        if($this->plus) {
            return true;
        }

        return $this->base !== 'r';
    }

    public function canOpenExistingFile(): bool
    {
        return 'x' !== $this->base;
    }

    public function canOpenNewFile(): bool
    {
        return $this->base !== 'r';
    }

    public function impliesExistingContentDeletion(): bool
    {
        return $this->base === 'w';
    }

    public function impliesPositioningCursorAtStart(): bool
    {
        return $this->base !== 'a';
    }

    public function impliesPositioningCursorAtEnd(): bool
    {
        return $this->base === 'a';
    }

    public function isBinary(): bool
    {
        return $this->flag === 'b';
    }

    public function isText(): bool
    {
        return $this->isBinary() === false;
    }

    public function toString(): string
    {
        return $this->getMode();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}