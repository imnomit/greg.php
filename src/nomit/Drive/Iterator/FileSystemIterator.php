<?php

namespace nomit\Drive\Iterator;

use nomit\Drive\Resource\ResourceInterface;
use nomit\Utility\Bitmask\Bitmask;
use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Stringable;
use nomit\Utility\Enumeration\EnumerationInterface;

class FileSystemIterator extends PathnameIterator
{

    protected BitmaskInterface $flags;

    public function __construct(
        ResourceInterface $resource,
        int|BitmaskInterface|EnumerationInterface|string|Stringable|callable $flags = null
    )
    {
        $this->flags = new Bitmask(0);

        $filters = func_get_args();

        foreach($filters as $filter) {
            if(ConcernUtility::isIntegerable($filter)) {
                $filter = new Bitmask($filter);

                if($filter->has(CurrentModeIteratorEnumeration::CURRENT_AS_PATHNAME)) {
                    $this->flags->add(CurrentModeIteratorEnumeration::CURRENT_AS_PATHNAME);
                } else if($filter->has(CurrentModeIteratorEnumeration::CURRENT_AS_BASENAME)) {
                    $this->flags->add(CurrentModeIteratorEnumeration::CURRENT_AS_BASENAME);
                } else if($filter->has(CurrentModeIteratorEnumeration::CURRENT_AS_FILE)) {
                    $this->flags->add(CurrentModeIteratorEnumeration::CURRENT_AS_FILE);
                } else if($filter->has(CurrentModeIteratorEnumeration::CURRENT_AS_SELF)) {
                    $this->flags->add(CurrentModeIteratorEnumeration::CURRENT_AS_SELF);
                } else if($filter->has(KeyModeIteratorEnumeration::KEY_AS_PATHNAME)) {
                    $this->flags->add(KeyModeIteratorEnumeration::KEY_AS_PATHNAME);
                } else if($filter->has(KeyModeIteratorEnumeration::KEY_AS_FILENAME)) {
                    $this->flags->add(KeyModeIteratorEnumeration::KEY_AS_FILENAME);
                }
            }
        }

        parent::__construct(
            $resource->getPathname(false),
            $filters
        );
    }

    public function current(): mixed
    {
        if($this->valid()) {
            if($this->flags->has(CurrentModeIteratorEnumeration::CURRENT_AS_SELF)) {
                return $this;
            } else if($this->flags->has(CurrentModeIteratorEnumeration::CURRENT_AS_PATHNAME)) {
                return $this->getCurrentFile()
                    ->getPathname();
            } else if($this->flags->has(CurrentModeIteratorEnumeration::CURRENT_AS_BASENAME)) {
                return $this->getCurrentFile()
                    ->getBasename();
            }

            return $this->getCurrentFile();
        }

        return null;
    }

    public function key(): mixed
    {
        if($this->flags->has(KeyModeIteratorEnumeration::KEY_AS_FILENAME)) {
            return $this->getCurrentFile()
                ->getFilename();
        }

        return $this->getCurrentFile()
            ->getPathname();
    }

    public function seek(mixed $offset): void
    {
        if(ConcernUtility::isIntegerable($offset)) {
            $this->index = ConcernUtility::toInteger($offset);
        } else {
            foreach($this->keys as $index => $key) {
                $match = $this->matches[$key];

                if($this->flags->has(KeyModeIteratorEnumeration::KEY_AS_FILENAME)) {
                    if($match->getFilename() === $offset) {
                        $this->index = $index;

                        return;
                    }
                } else if($match->getPathname() === $offset) {
                    $this->index = $index;

                    return;
                }
            }
        }
    }

}