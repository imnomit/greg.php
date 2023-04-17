<?php

namespace nomit\Drive\Iterator;

use nomit\Drive\Exception\InvalidArgumentException;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\ResourceInterface;
use nomit\Drive\Utility\FileSystemUtility;
use nomit\Drive\Utility\ListModeEnumeration;
use nomit\Utility\Bitmask\Bitmask;
use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Concern\ConcernUtility;

class PathnameIterator implements PathnameIteratorInterface
{

    protected PathnameInterface $pathname;

    protected ?BitmaskInterface $bitmask = null;

    protected ?array $globFilters = null;

    protected ?array $callableFilters = null;

    protected ?array $globSearchPatterns = null;

    protected ?array $matches = null;

    protected ?array $keys = null;

    protected int $index;

    public function __construct(
        PathnameInterface $pathname,
        protected array $filters
    )
    {
        if(!$pathname->getLocalAdapter()
            ->isDirectory($pathname)
        ) {
            throw new InvalidArgumentException(sprintf('The resource referenced by the supplied pathname, "%s", is not a directory.', $pathname->getPathname()));
        }

        $this->pathname = $pathname;
        $this->index = -1;
    }

    public function getBitmask(): ?BitmaskInterface
    {
        return $this->bitmask;
    }

    public function getGlobFilters(): array
    {
        return $this->globFilters ?? [];
    }

    public function getCallableFilters(): array
    {
        return $this->callableFilters ?? [];
    }

    public function getGlobSearchPatterns(): array
    {
        return $this->globSearchPatterns ?? [];
    }

    final protected function getKeys(): array
    {
        if($this->keys === null) {
            $this->getMatches();
        }

        return $this->keys;
    }

    final protected function getMatches(): array
    {
        if($this->matches === null) {
            $this->prepareFilters();

            $this->matches = [];

            $fileSystem = $this->pathname
                ->getAdapter()
                ->getFileSystem();

            $filenames = $this->pathname
                ->getLocalAdapter()
                ->list($this->pathname);

            foreach($filenames as $filename) {
                $childPathname = $this->pathname->getChild($filename);
                $file = $fileSystem->getFile($childPathname);

                if($this->applyBitmaskFilters($file)
                    && $this->applyGlobFilters($file)
                    && $this->applyCallableFilters($file)
                ) {
                    $this->matches[] = $file;
                }
            }

            $this->keys = array_keys($this->matches);
        }

        return $this->matches;
    }

    final protected function prepareFilters(PathnameIteratorInterface $iterator = null): void
    {
        if(
            $this->bitmask === null
            || $this->globFilters === null
            || $this->callableFilters === null
            || $this->globSearchPatterns === null
        ) {
            if($iterator !== null) {
                $this->bitmask = $iterator->getBitmask();
                $this->globFilters = $iterator->getGlobFilters();
                $this->callableFilters = $iterator->getCallableFilters();
                $this->globSearchPatterns = $iterator->getGlobSearchPatterns();
            } else {
                $this->bitmask = null;
                $this->globFilters = [];
                $this->callableFilters = [];
                $this->globSearchPatterns = [];

                $this->evaluateFilters($this->filters);

                if($this->bitmask === null) {
                    $this->bitmask = new Bitmask(ListModeEnumeration::LIST_ALL);
                } else if($this->bitmask->is(ListModeEnumeration::LIST_HIDDEN)
                    || $this->bitmask->is(ListModeEnumeration::LIST_VISIBLE)
                    || ($this->bitmask->has(ListModeEnumeration::LIST_HIDDEN) && $this->bitmask->has(ListModeEnumeration::LIST_VISIBLE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_HIDDEN) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_VISIBLE) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_HIDDEN) && $this->bitmask->has(ListModeEnumeration::LIST_VISIBLE) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                ) {
                    $this->bitmask->add(ListModeEnumeration::LIST_FILES)
                        ->add(ListModeEnumeration::LIST_DIRECTORIES)
                        ->add(ListModeEnumeration::LIST_LINKS)
                        ->add(ListModeEnumeration::LIST_OPAQUE);
                } else if(
                    $this->bitmask->is(ListModeEnumeration::LIST_FILES)
                    || $this->bitmask->is(ListModeEnumeration::LIST_DIRECTORIES)
                    || ($this->bitmask->has(ListModeEnumeration::LIST_FILES) && $this->bitmask->has(ListModeEnumeration::LIST_DIRECTORIES))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_FILES) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_DIRECTORIES) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_FILES) && $this->bitmask->has(ListModeEnumeration::LIST_DIRECTORIES) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                ) {
                    $this->bitmask->add(ListModeEnumeration::LIST_HIDDEN)
                        ->add(ListModeEnumeration::LIST_VISIBLE)
                        ->add(ListModeEnumeration::LIST_LINKS)
                        ->add(ListModeEnumeration::LIST_OPAQUE);
                } else if(
                    $this->bitmask->is(ListModeEnumeration::LIST_LINKS)
                    || $this->bitmask->is(ListModeEnumeration::LIST_OPAQUE)
                    || ($this->bitmask->has(ListModeEnumeration::LIST_LINKS) && $this->bitmask->has(ListModeEnumeration::LIST_OPAQUE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_LINKS) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_OPAQUE) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                    || ($this->bitmask->has(ListModeEnumeration::LIST_LINKS) && $this->bitmask->has(ListModeEnumeration::LIST_OPAQUE) && $this->bitmask->has(ListModeEnumeration::LIST_RECURSIVE))
                ) {
                    $this->bitmask->add(ListModeEnumeration::LIST_HIDDEN)
                        ->add(ListModeEnumeration::LIST_VISIBLE);
                } else if($this->bitmask->is(ListModeEnumeration::LIST_RECURSIVE)) {
                    $this->bitmask->add(ListModeEnumeration::LIST_ALL);
                }

                foreach($this->globFilters as $index => $glob) {
                    $parts = explode('/', $glob);

                    if(($partsCount = count($parts)) > 1) {
                        $maximum = $partsCount - 2;
                        $path = '';

                        for($i = 0; $i < $maximum; $i++) {
                            $path .= ($path ? '/' : '') . $parts[$i];
                        }

                        $this->globFilters[$index] = FileSystemUtility::normalizePathname('*/' . $this->pathname->getPathname() . '/' . $glob);
                    } else {
                        $this->globFilters[$index] = $glob;
                    }
                }
            }
        }
    }

    final protected function evaluateFilters(array $filters): void
    {
        foreach($filters as $filter) {
            if(ConcernUtility::isIntegerable($filter)) {
                $filter = ConcernUtility::toInteger($filter);

                if($this->bitmask === null) {
                    $this->bitmask = new Bitmask($filter);
                } else {
                    $this->bitmask->add($filter);
                }
            } else if(ConcernUtility::isStringable($filter)) {
                $this->globFilters[] = FileSystemUtility::normalizePathname(ConcernUtility::toString($filter));
            } else if(ConcernUtility::isCallable($filter)) {
                $this->callableFilters[] = ConcernUtility::toCallback($filter);
            } else if(ConcernUtility::isIterable($filter)) {
                $this->evaluateFilters(ConcernUtility::toArray($filter));
            } else {
                if(is_object($filter)) {
                    $type = get_class($filter);
                } else {
                    ob_start();

                    var_dump($filter);

                    $type = ob_get_contents();

                    ob_end_clean();
                }

                throw new InvalidArgumentException(
                    sprintf(
                        'The supplied filter, of type "%s", is not supported by the "%s" method of the "%s" iterator class.',
                        $type,
                        __FUNCTION__,
                        get_class($this)
                    )
                );
            }
        }
    }

    final protected function applyBitmaskFilters(ResourceInterface $resource): bool
    {
        $basename = $resource->getBasename();

        return !(!$this->bitmask->has(ListModeEnumeration::LIST_HIDDEN)
            && $basename[0] === '.'
            || !$this->bitmask->has(ListModeEnumeration::LIST_VISIBLE)
            && $basename[0] !== '.'
            || !$this->bitmask->has(ListModeEnumeration::LIST_FILES)
            && $resource->isFile()
            || !$this->bitmask->has(ListModeEnumeration::LIST_DIRECTORIES)
            && $resource->isDirectory()
            || !$this->bitmask->has(ListModeEnumeration::LIST_LINKS)
            && ($resource->isLink())
            || !$this->bitmask->has(ListModeEnumeration::LIST_OPAQUE)
            && !$resource->isLink());
    }

    final protected function applyGlobFilters(ResourceInterface $resource): bool
    {
        foreach($this->globFilters as $globFilter) {
            if(!fnmatch($globFilter, $resource->getPathname())) {
                return false;
            }
        }

        return true;
    }

    final protected function applyCallableFilters(ResourceInterface $resource): bool
    {
        foreach($this->callableFilters as $callableFilter) {
            if(!$callableFilter($resource->getPathname(), $resource)) {
                return false;
            }
        }

        return true;
    }

    final protected function applyGlobSearchPatterns(ResourceInterface $resource = null): bool
    {
        if($this->globSearchPatterns !== null
            && count($this->globSearchPatterns) > 0
        ) {
            if($resource === null) {
                $resource = $this->getCurrentFile();
            }

            foreach($this->globSearchPatterns as $globSearchPattern) {
                if(fnmatch($globSearchPattern, $resource->getPathname())) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    final protected function getCurrentFile(): ResourceInterface
    {
        $matches = $this->getMatches();
        $keys = $this->getKeys();

        return $matches[$keys[$this->index]];
    }

    public function current(): mixed
    {
        return $this->getCurrentFile();
    }

    public function next(): void
    {
        $keys = $this->getKeys();

        if($this->index < count($keys)) {
            $this->index++;
        }
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function valid(): bool
    {
        $keys = $this->getKeys();

        return $this->index >= 0
            && $this->index < count($keys);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function seek(int $offset): void
    {
        $this->index = $offset;
    }

    public function toArray(): array
    {
        return $this->getMatches();
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}