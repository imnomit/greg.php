<?php

namespace nomit\Drive\Resource\File;

use nomit\Drive\Event\AppendFileEvent;
use nomit\Drive\Event\FileSystemEvents;
use nomit\Drive\Event\TouchFileEvent;
use nomit\Drive\Event\TruncateFileEvent;
use nomit\Drive\Event\WriteFileEvent;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\ResourceTrait;
use nomit\Drive\Stream\StreamInterface;
use nomit\Stream\StreamModeInterface;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;

class File extends \SplFileInfo implements FileInterface
{

    use ResourceTrait;

    public function __construct(
        protected readonly PathnameInterface $pathname
    )
    {
        parent::__construct($this->pathname->getPathname());

        $this->fileSystem = $this->pathname->getAdapter()->getFileSystem();
    }

    public function touch(\DateTimeInterface|Integerable|int|string|Stringable $modificationTime = 'NOW', \DateTimeInterface|Integerable|int|string|Stringable $accessTime = null, bool $create = true): bool
    {
        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $exists = $this->pathname->getAdapter()
                ->exists($this->pathname);
        } else {
            $exists = null;
        }

        $modificationTime = ConcernUtility::toDateTime($modificationTime);
        $accessTime = $accessTime === null
            ? $modificationTime
            : ConcernUtility::toDateTime($accessTime);

        $this->pathname->getAdapter()
            ->touch($this->pathname, $modificationTime, $accessTime, $create);

        if($dispatcher) {
            $event = new TouchFileEvent(
                $this->fileSystem,
                $this,
                $modificationTime,
                $accessTime,
                $create && !$exists
            );

            $dispatcher->dispatch($event, FileSystemEvents::TOUCH_EVENT);
        }

        return true;
    }

    public function isExecutable(): bool
    {
        return $this->pathname->getAdapter()
            ->isExecutable($this->pathname);
    }

    public function read(): string
    {
        return $this->pathname->getAdapter()
            ->read($this->pathname);
    }

    public function write(string $content, bool $create = true): int
    {
        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $exists = $this->pathname->getAdapter()
                ->exists($this->pathname);
        } else {
            $exists = null;
        }

        $result = $this->pathname->getAdapter()
            ->write($this->pathname, $content, $create);

        if($dispatcher) {
            $event = new WriteFileEvent($this->fileSystem, $this, $content, $create && !$exists);

            $dispatcher->dispatch($event, FileSystemEvents::WRITE_EVENT);
        }

        return $result;
    }

    public function append(string $content, bool $create = true): int
    {
        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $exists = $this->pathname->getAdapter()
                ->exists($this->pathname);
        } else {
            $exists = null;
        }

        $result = $this->pathname->getAdapter()
            ->append($this->pathname, $content, $create);

        if($dispatcher) {
            $event = new AppendFileEvent($this->fileSystem, $this, $content, $create && !$exists);

            $dispatcher->dispatch($event, FileSystemEvents::APPEND_EVENT);
        }

        return $result;
    }

    public function truncate(int $size = 0): FileInterface
    {
        $this->pathname->getAdapter()
            ->truncate($this->pathname, $size);

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = new TruncateFileEvent($this->fileSystem, $this, $size);

            $dispatcher->dispatch($event, FileSystemEvents::TRUNCATE_EVENT);
        }

        return $this;
    }

    public function getStream(StreamModeInterface|string|Stringable $mode = null): StreamInterface
    {
        return $this->pathname->getAdapter()
            ->getStream($this->pathname);
    }

    public function getStreamUrl(): string
    {
        return $this->pathname->getAdapter()
            ->getStreamUrl($this->pathname);
    }

}