<?php

namespace nomit\Drive\Pathname;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Utility\Concern\Stringable;

interface PathnameInterface extends Stringable
{

    public function getPathname(): string;

    public function getBasename(): string;

    public function getAdapter(): AdapterInterface;

    public function getLocalAdapter(): AdapterInterface;

    public function getLocalPathname(): string;

    public function getParent(): self;

    public function getChild(string|self $basename): self;

}