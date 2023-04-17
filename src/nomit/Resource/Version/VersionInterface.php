<?php

namespace nomit\Resource\Version;

use nomit\Utility\Concern\Stringable;

interface VersionInterface extends Stringable
{

    public function setVersion(string|int $version): self;

    public function getVersion(): string|int;

    public function setFormat(string $format): self;

    public function getFormat(): string;

    public function interpolate(string $path): string;

    public function compare(VersionInterface $version): bool;

}