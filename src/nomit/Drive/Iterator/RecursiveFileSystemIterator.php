<?php

namespace nomit\Drive\Iterator;

use RecursiveIterator;

class RecursiveFileSystemIterator extends FileSystemIterator implements \RecursiveIterator
{

    public function hasChildren(): bool
    {
        return $this->valid()
            && $this->getCurrentFile()->isDirectory()
            && $this->applyGlobSearchPatterns();
    }

    public function getChildren(): ?RecursiveIterator
    {
        $iterator = new RecursiveFileSystemIterator(
            $this->matches[$this->keys[$this->index]],
            $this->filters,
            $this->flags
        );

        $iterator->prepareFilters($this);

        return $iterator;
    }

}