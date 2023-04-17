<?php

namespace nomit\Drive\Resource\Directory;

use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\ResourceTrait;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;
use Traversable;

class Directory extends \DirectoryIterator implements DirectoryInterface
{

    use ResourceTrait;

    public function __construct(
        protected readonly PathnameInterface $pathname
    )
    {
        parent::__construct($this->pathname->getPathname());

        $this->fileSystem = $this->pathname->getAdapter()->getFileSystem();
    }

    public function list(callable|Integerable|int|\Closure|string|Stringable $filter, ...$arguments): array
    {
        $iterator = $this->pathname->getAdapter()
            ->getIterator($this->pathname, func_get_args());

        if ($iterator instanceof Arrayable
            || method_exists($iterator, 'toArray')
        ) {
            return $iterator->toArray();
        }

        $files = [];

        foreach ($iterator as $file) {
            $files[] = $file;
        }

        return $files;
    }

}