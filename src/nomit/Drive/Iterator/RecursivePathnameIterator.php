<?php

namespace nomit\Drive\Iterator;

use RecursiveIterator;

final class RecursivePathnameIterator extends PathnameIterator implements \RecursiveIterator
{

    public function hasChildren(): bool
    {
        return $this->valid()
            && $this->current()->isDirectory()
            && $this->applyGlobSearchPatterns();
    }

    public function getChildren(): ?RecursiveIterator
    {
        $iterator = new RecursivePathnameIterator(
            $this->getCurrentFile()->getPathname(false),
            $this->filters
        );

        $iterator->prepareFilters($this);

        return $iterator;
    }

}