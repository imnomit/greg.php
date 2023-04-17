<?php

namespace nomit\Stream;

use nomit\Utility\Concern\Stringable;

interface StreamModeInterface extends Stringable
{

    public function getMode(): string;

    public function canRead(): bool;

    public function canWrite(): bool;

    public function canOpenExistingFile(): bool;

    public function canOpenNewFile(): bool;

    public function impliesExistingContentDeletion(): bool;

    public function impliesPositioningCursorAtStart(): bool;

    public function impliesPositioningCursorAtEnd(): bool;

    public function isBinary(): bool;

    public function isText(): bool;

}