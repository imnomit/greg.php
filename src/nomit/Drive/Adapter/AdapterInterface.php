<?php

namespace nomit\Drive\Adapter;

use nomit\Drive\FileSystemInterface;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Utility\OperationEnumeration;
use nomit\Drive\Stream\StreamInterface;
use nomit\Stream\StreamModeInterface;
use nomit\Utility\Bitmask\BitmaskInterface;

interface AdapterInterface
{

    public function setFileSystem(?FileSystemInterface $fileSystem): self;

    public function getFileSystem(): FileSystemInterface;

    public function getPrimaryAdapter(): self;

    public function setParentAdapter(self $adapter = null): self;

    public function getParentAdapter(): self|null;

    public function resolveLocalPathname(PathnameInterface $pathname, self &$localAdapter, string &$localAdapterPathname): self;

    public function isFile(PathnameInterface $pathname): bool;

    public function isDirectory(PathnameInterface $pathname): bool;

    public function isLink(PathnameInterface $pathname): bool;

    public function setAccessTime(PathnameInterface $pathname, \DateTimeInterface $dateTime): self;

    public function getAccessTime(PathnameInterface $pathname): \DateTimeInterface;

    public function getCreationTime(PathnameInterface $pathname): \DateTimeInterface;

    public function setModificationTime(PathnameInterface $pathname, \DateTimeInterface $dateTime): self;

    public function getModificationTime(PathnameInterface $pathname): \DateTimeInterface;

    public function touch(iterable|PathnameInterface $pathname, \DateTimeInterface $modificationTime, \DateTimeInterface $accessTime, bool $create): self;

    public function getSize(PathnameInterface $pathname, bool $recursive): int;

    public function setOwner(iterable|PathnameInterface $pathname, string|int $owner): self;

    public function getOwner(PathnameInterface $pathname): string|int;

    public function setGroup(iterable|PathnameInterface $pathname, int|string $group): self;

    public function getGroup(PathnameInterface $pathname): int|string;

    public function setMode(iterable|PathnameInterface $pathname, int|string $mode): self;

    public function getMode(PathnameInterface $pathname): int|string;

    public function isReadable(PathnameInterface $pathname): bool;

    public function isWritable(PathnameInterface $pathname): bool;

    public function isExecutable(PathnameInterface $pathname): bool;

    public function exists(iterable|PathnameInterface $pathname): bool;

    public function delete(PathnameInterface $pathname, bool $recursive, bool $force): self;

    public function copyTo(PathnameInterface $sourcePathname, PathnameInterface $destinationPathname, OperationEnumeration|int|BitmaskInterface $flags): self;

    public function copyFrom(PathnameInterface $destinationPathname, PathnameInterface $sourcePathname, OperationEnumeration|int|BitmaskInterface $flags): self;

    public function moveTo(PathnameInterface $sourcePathname, PathnameInterface $destinationPathname, OperationEnumeration|int|BitmaskInterface $flags): self;

    public function moveFrom(PathnameInterface $destinationPathname, PathnameInterface $sourcePathname, OperationEnumeration|int|BitmaskInterface $flags): self;

    public function createDirectory(PathnameInterface $pathname, bool $createParents): DirectoryInterface;

    public function createFile(PathnameInterface $pathname, bool $createParents): FileInterface;

    public function write(PathnameInterface $pathname, string $contents, bool $create): int;

    public function append(PathnameInterface $pathname, string $content, bool $create): int;

    public function read(PathnameInterface $pathname): string;

    public function truncate(PathnameInterface $pathname, int $size): self;

    public function getStream(PathnameInterface $pathname, StreamModeInterface|string $mode = 'r'): StreamInterface;

    public function getStreamUrl(PathnameInterface $pathname): string;

    public function list(PathnameInterface $pathname): array;

    public function count(PathnameInterface $pathname, array $filters): int;

    public function getIterator(PathnameInterface $pathname, array $filters): \Iterator;

}