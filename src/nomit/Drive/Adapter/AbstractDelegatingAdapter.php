<?php

namespace nomit\Drive\Adapter;

use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Utility\OperationEnumeration;
use nomit\Drive\Stream\StreamInterface;
use nomit\Stream\StreamModeInterface;
use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Concern\ConcernUtility;

abstract class AbstractDelegatingAdapter extends AbstractAdapter
{

    abstract protected function delegate(PathnameInterface $pathname = null): self;

    public function resolveLocalPathname(PathnameInterface $pathname, AdapterInterface &$localAdapter, string &$localAdapterPathname): AdapterInterface
    {
        return $this->delegate($pathname)
            ->resolveLocalPathname($pathname, $localAdapter, $localAdapterPathname);
    }

    public function isFile(PathnameInterface $pathname): bool
    {
        return $this->delegate($pathname)
            ->isFile($pathname);
    }

    public function isDirectory(PathnameInterface $pathname): bool
    {
        return $this->delegate($pathname)
            ->isDirectory($pathname);
    }

    public function isLink(PathnameInterface $pathname): bool
    {
        return $this->delegate($pathname)
            ->isLink($pathname);
    }

    public function setAccessTime(PathnameInterface $pathname, \DateTimeInterface $dateTime): AdapterInterface
    {
        $this->delegate($pathname)
            ->setAccessTime($pathname, $dateTime);

        return $this;
    }

    public function getAccessTime(PathnameInterface $pathname): \DateTimeInterface
    {
        return $this->delegate($pathname)
            ->getAccessTime($pathname);
    }

    public function getCreationTime(PathnameInterface $pathname): \DateTimeInterface
    {
        return $this->delegate($pathname)
            ->getCreationTime($pathname);
    }

    public function setModificationTime(PathnameInterface $pathname, \DateTimeInterface $dateTime): AdapterInterface
    {
        $this->delegate($pathname)
            ->setModificationTime($pathname, $dateTime);

        return $this;
    }

    public function getModificationTime(PathnameInterface $pathname): \DateTimeInterface
    {
        return $this->delegate($pathname)
            ->getModificationTime($pathname);
    }

    public function touch(iterable|PathnameInterface $pathname, \DateTimeInterface $modificationTime, \DateTimeInterface $accessTime, bool $create): AdapterInterface
    {
        foreach(ConcernUtility::toIterable($pathname) as $path) {
            $this->delegate($path)
                ->touch($path, $modificationTime, $accessTime, $create);
        }

        return $this;
    }

    public function getSize(PathnameInterface $pathname, bool $recursive): int
    {
        return $this->delegate($pathname)
            ->getSize($pathname, $recursive);
    }

    public function setOwner(iterable|PathnameInterface $pathname, int|string $owner): AdapterInterface
    {
        foreach(ConcernUtility::toIterable($pathname) as $path) {
            $this->delegate($path)
                ->setOwner($path, $owner);
        }

        return $this;
    }

    public function getOwner(PathnameInterface $pathname): string|int
    {
        return $this->delegate($pathname)
            ->getOwner($pathname);
    }

    public function setGroup(iterable|PathnameInterface $pathname, int|string $group): AdapterInterface
    {
        foreach(ConcernUtility::toIterable($pathname) as $path) {
            $this->delegate($path)
                ->setGroup($path, $group);
        }

        return $this;
    }

    public function getGroup(PathnameInterface $pathname): int|string
    {
        return $this->delegate($pathname)
            ->getGroup($pathname);
    }

    public function setMode(iterable|PathnameInterface $pathname, int|string $mode): AdapterInterface
    {
        foreach(ConcernUtility::toIterable($pathname) as $path) {
            $this->delegate($path)
                ->setMode($path, $mode);
        }

        return $this;
    }

    public function getMode(PathnameInterface $pathname): int|string
    {
        return $this->delegate($pathname)
            ->getMode($pathname);
    }

    public function isReadable(PathnameInterface $pathname): bool
    {
        return $this->delegate($pathname)
            ->isReadable($pathname);
    }

    public function isWritable(PathnameInterface $pathname): bool
    {
        return $this->delegate($pathname)
            ->isWritable($pathname);
    }

    public function isExecutable(PathnameInterface $pathname): bool
    {
        return $this->delegate($pathname)
            ->isExecutable($pathname);
    }

    public function exists(iterable|PathnameInterface $pathname): bool
    {
        foreach(ConcernUtility::toIterable($pathname) as $path) {
            if(!$this->delegate($path)->exists($path)) {
                return false;
            }
        }

        return true;
    }

    public function delete(PathnameInterface $pathname, bool $recursive, bool $force): AdapterInterface
    {
        $this->delegate($pathname)
            ->delete($pathname, $recursive, $force);

        return $this;
    }

    public function copyTo(PathnameInterface $sourcePathname, PathnameInterface $destinationPathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        $this->delegate($sourcePathname)
            ->copyTo($sourcePathname, $destinationPathname, $flags);

        return $this;
    }

    public function copyFrom(PathnameInterface $destinationPathname, PathnameInterface $sourcePathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        $this->delegate($sourcePathname)
            ->copyFrom($destinationPathname, $sourcePathname, $flags);

        return $this;
    }

    public function moveTo(PathnameInterface $sourcePathname, PathnameInterface $destinationPathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        $this->delegate($sourcePathname)
            ->moveTo($sourcePathname, $destinationPathname, $flags);

        return $this;
    }

    public function moveFrom(PathnameInterface $destinationPathname, PathnameInterface $sourcePathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        $this->delegate($sourcePathname)
            ->moveFrom($destinationPathname, $sourcePathname, $flags);


        return $this;
    }

    public function createDirectory(PathnameInterface $pathname, bool $createParents): DirectoryInterface
    {
        return $this->delegate($pathname)
            ->createDirectory($pathname, $createParents);
    }

    public function createFile(PathnameInterface $pathname, bool $createParents): FileInterface
    {
        return $this->delegate($pathname)
            ->createFile($pathname, $createParents);
    }

    public function read(PathnameInterface $pathname): string
    {
        return $this->delegate($pathname)
            ->read($pathname);
    }

    public function write(PathnameInterface $pathname, string $contents, bool $create): int
    {
        return $this->delegate($pathname)
            ->write($pathname, $contents, $create);
    }

    public function append(PathnameInterface $pathname, string $content, bool $create): int
    {
        return $this->delegate($pathname)
            ->append($pathname, $content, $create);
    }

    public function truncate(PathnameInterface $pathname, int $size): AdapterInterface
    {
        $this->delegate($pathname)
            ->truncate($pathname, $size);

        return $this;
    }

    public function getStream(PathnameInterface $pathname, StreamModeInterface|string $mode = 'r'): StreamInterface
    {
        return $this->delegate($pathname)
            ->getStream($pathname, $mode);
    }

    public function getStreamUrl(PathnameInterface $pathname): string
    {
        return $this->delegate($pathname)
            ->getStreamUrl($pathname);
    }

    public function list(PathnameInterface $pathname): array
    {
        return $this->delegate($pathname)
            ->list($pathname);
    }

    public function count(PathnameInterface $pathname, array $filters): int
    {
        return $this->delegate($pathname)
            ->count($pathname, $filters);
    }

    public function getIterator(PathnameInterface $pathname, array $filters): \Iterator
    {
        return $this->delegate($pathname)
            ->getIterator($pathname, $filters);
    }

}