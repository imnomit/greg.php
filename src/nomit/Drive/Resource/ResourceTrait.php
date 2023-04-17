<?php

namespace nomit\Drive\Resource;

use nomit\Drive\Event\CopyEvent;
use nomit\Drive\Event\CreateDirectoryEvent;
use nomit\Drive\Event\CreateFileEvent;
use nomit\Drive\Event\DeleteDirectoryEvent;
use nomit\Drive\Event\DeleteFileEvent;
use nomit\Drive\Event\FileSystemEvents;
use nomit\Drive\Event\MoveEvent;
use nomit\Drive\Event\SetGroupDirectoryEvent;
use nomit\Drive\Event\SetGroupFileEvent;
use nomit\Drive\Event\SetModeDirectoryEvent;
use nomit\Drive\Event\SetModeFileEvent;
use nomit\Drive\Event\SetOwnerDirectoryEvent;
use nomit\Drive\Event\SetOwnerFileEvent;
use nomit\Drive\Event\TouchFileEvent;
use nomit\Drive\FileSystemInterface;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Plugin\FilePluginProviderInterface;
use nomit\Drive\Plugin\ResourcePluginInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Utility\FileSystemUtility;
use nomit\Drive\Utility\OperationEnumeration;
use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Utility\Bitmask\Bitmask;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\Concern\Stringable;

trait ResourceTrait
{

    protected readonly FileSystemInterface $fileSystem;

    public function getFileSystem(): FileSystemInterface
    {
        return $this->fileSystem;
    }

    public function getPathname(bool $asString = true): string|PathnameInterface
    {
        return $asString
            ? $this->pathname->getPathname()
            : $this->pathname;
    }

    public function getBasename(string $suffix = null): string
    {
        return basename($this->pathname, $suffix);
    }

    public function getExtension(): string
    {
        $basename = $this->getBasename();
        $position = strpos($basename, '.');

        return $position === false
            ? ''
            : substr($basename, $position + 1);
    }

    public function getDirectory(): string
    {
        return $this->pathname->getParent()
            ->getPathname();
    }

    public function getParent(): ?ResourceInterface
    {
        if($this->pathname->getPathname() !== '/') {
            return $this->isDirectory()
                ? $this->fileSystem->getDirectory($this->getDirectory())
                : $this->fileSystem->getFile($this->getDirectory());
        }

        return null;
    }

    public function isFile(): bool
    {
        return $this->pathname->getAdapter()
            ->isFile($this->pathname);
    }

    public function isDirectory(): bool
    {
        return $this->pathname->getAdapter()
            ->isDirectory($this->pathname);
    }

    public function setAccessTime(\DateTimeInterface|Integerable|int|string|Stringable $dateTime = null): ResourceInterface
    {
        $this->pathname->getAdapter()
            ->setAccessTime($this->pathname, ConcernUtility::toDateTime($dateTime));

        return $this;
    }

    public function getAccessTime(): \DateTimeInterface
    {
        return $this->pathname->getAdapter()
            ->getAccessTime($this->pathname);
    }

    public function getCreationTime(): \DateTimeInterface
    {
        return $this->pathname->getAdapter()
            ->getCreationTime($this->pathname);
    }

    public function setModificationTime(\DateTimeInterface|Integerable|int|string|Stringable $dateTime = null): ResourceInterface
    {
        $this->pathname->getAdapter()
            ->setModificationTime($this->pathname, ConcernUtility::toDateTime($dateTime));

        return $this;
    }

    public function getSize(bool $recursive = false): int|false
    {
        return $this->pathname->getAdapter()
            ->getSize($this->pathname, $recursive);
    }

    public function getModificationTime(): \DateTimeInterface
    {
        return $this->pathname->getAdapter()
            ->getModificationTime($this->pathname);
    }

    public function setOwner(int|string $user): ResourceInterface
    {
        $this->pathname->getAdapter()
            ->setOwner($this->pathname, $user);

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = $this->isDirectory()
                ? new SetOwnerDirectoryEvent($this->fileSystem, $this, $user)
                : new SetOwnerFileEvent($this->fileSystem, $this, $user);

            $dispatcher->dispatch($event, FileSystemEvents::SET_OWNER_EVENT);
        }

        return $this;
    }

    public function getOwner(): int|string
    {
        return $this->pathname->getAdapter()
            ->getOwner($this->pathname);
    }

    public function setGroup(int|string $group): ResourceInterface
    {
        $this->pathname->getAdapter()
            ->setGroup($this->pathname, $group);

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = $this->isDirectory()
                ? new SetGroupDirectoryEvent($this->fileSystem, $this, $group)
                : new SetGroupFileEvent($this->fileSystem, $this, $group);

            $dispatcher->dispatch($event, FileSystemEvents::SET_GROUP_EVENT);
        }

        return $this;
    }

    public function getGroup(): int|string
    {
        return $this->pathname->getAdapter()
            ->getGroup($this->pathname);
    }

    public function setMode(int $mode): ResourceInterface
    {
        $this->pathname->getAdapter()
            ->setMode($this->pathname, $mode);

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = $this->isDirectory()
                ? new SetModeDirectoryEvent($this->fileSystem, $this, $mode)
                : new SetModeFileEvent($this->fileSystem, $this, $mode);

            $dispatcher->dispatch($event, FileSystemEvents::SET_MODE_EVENT);
        }

        return $this;
    }

    public function getMode(): int
    {
        return $this->pathname->getAdapter()
            ->getMode();
    }

    public function isReadable(): bool
    {
        return $this->pathname->getAdapter()
            ->isReadable($this->pathname);
    }

    public function isWritable(): bool
    {
        return $this->pathname->getAdapter()
            ->isWritable($this->pathname);
    }

    public function exists(): bool
    {
        return $this->pathname->getAdapter()
            ->exists($this->pathname);
    }

    public function delete(bool $recursive = false, bool $force = false): bool
    {
        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = $this->isDirectory()
                ? new DeleteDirectoryEvent($this->fileSystem, $this, $recursive)
                : new DeleteFileEvent($this->fileSystem, $this);

            $dispatcher->dispatch($event, FileSystemEvents::BEFORE_DELETE_EVENT);
        }

        $this->pathname->getAdapter()
            ->delete($this->pathname, $recursive, $force);

        if($dispatcher) {
            $event = $this->isDirectory()
                ? new DeleteDirectoryEvent($this->fileSystem, $this, $recursive)
                : new DeleteFileEvent($this->fileSystem, $this);

            $dispatcher->dispatch($event, FileSystemEvents::DELETE_EVENT);
        }

        return true;
    }

    public function copyTo(ResourceInterface $destination, bool $recursive = false, bool $overwrite = false, bool $createParents = false): ResourceInterface
    {
        $mask = new Bitmask(0);

        if($recursive) {
            $mask->add(OperationEnumeration::OPERATION_RECURSIVE);
        }

        if($createParents) {
            $mask->add(OperationEnumeration::OPERATION_PARENTS);
        }

        if($overwrite) {
            $mask->add(OperationEnumeration::OPERATION_MERGE);
        }

        $this->pathname->getAdapter()
            ->copyTo(
                $this->pathname,
                $destination->getPathname(false),
                $mask
            );

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = new CopyEvent($this->fileSystem, $this, $destination, $recursive, $overwrite, $createParents);

            $dispatcher->dispatch($event, FileSystemEvents::COPY_EVENT);
        }

        return $this;
    }

    public function moveTo(ResourceInterface $destination, bool $overwrite = false, bool $createParents = false): ResourceInterface
    {
        $mask = new Bitmask(0);

        if($createParents) {
            $mask->add(OperationEnumeration::OPERATION_PARENTS);
        }

        if($overwrite) {
            $mask->add(OperationEnumeration::OPERATION_MERGE);
        }

        $this->pathname->getAdapter()
            ->moveTo(
                $this->pathname,
                $destination->getPathname(false),
                $mask
            );

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = new MoveEvent($this->fileSystem, $this, $destination, $overwrite, $createParents);

            $dispatcher->dispatch($event, FileSystemEvents::MOVE_EVENT);
        }

        return $this;
    }

    public function createDirectory(bool $createParents = false): DirectoryInterface
    {
        $directory = $this->pathname->getAdapter()
            ->createDirectory($this->pathname, $createParents);

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = new CreateDirectoryEvent($this->fileSystem, $this, $createParents);

            $dispatcher->dispatch($event, FileSystemEvents::CREATE_DIRECTORY_EVENT);
        }

        return $directory;
    }

    public function createFile(bool $createParents = false): FileInterface
    {
        $file = $this->pathname->getAdapter()
            ->createFile($this->pathname, $createParents);

        $dispatcher = $this->fileSystem->getEventDispatcher();

        if($dispatcher) {
            $event = new CreateFileEvent($this->fileSystem, $this, $createParents);

            $dispatcher->dispatch($event, FileSystemEvents::CREATE_FILE_EVENT);
        }

        return $file;
    }

    public function count(int|string|Stringable|\Closure|callable $filter = null, mixed $variable = null): int
    {
        return $this->pathname->getAdapter()
            ->count($this->pathname, func_get_args());
    }

    public function getIterator(int|string|Stringable|\Closure|callable $filter = null, mixed $variable = null): \Traversable
    {
        return $this->pathname->getAdapter()
            ->getIterator($this->pathname, func_get_args());
    }

    public function hasPlugin(string|Stringable $name): bool
    {
        $manager = $this->fileSystem->getPluginManager();

        return $manager
            && $manager->has($name)
            && $manager->get($name)->providesResource($this);
    }

    public function getPlugin(string|Stringable $name): ?ResourcePluginInterface
    {
        $manager = $this->fileSystem->getPluginManager();

        if($manager
            && $manager->has($name)
        ) {
            $plugin = $manager->get($name);

            if($plugin->providesResource($this)) {
                return $plugin->fromResource($this);
            }
        }

        return null;
    }

    public function toString(): string
    {
        return $this->getStreamUrl();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}