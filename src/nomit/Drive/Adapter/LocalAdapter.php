<?php

namespace nomit\Drive\Adapter;

use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Exception\InvalidArgumentException;
use nomit\Drive\Exception\OperationException;
use nomit\Drive\Exception\Resource\ResourceException;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Plugin\Hash\HashAwareAdapterInterface;
use nomit\Drive\Plugin\Link\LinkAwareAdapterInterface;
use nomit\Drive\Plugin\MimeType\MimeTypeAwareAdapterInterface;
use nomit\Drive\Plugin\Space\SpaceAwareAdapterInterface;
use nomit\Drive\Utility\FileSystemUtility;
use nomit\Drive\Utility\ResourceExistenceAsserter;
use nomit\Utility\Callback\CallbackUtility;
use nomit\Utility\Concern\Stringable;

class LocalAdapter extends AbstractAdapter implements SpaceAwareAdapterInterface,
    HashAwareAdapterInterface,
    LinkAwareAdapterInterface,
    MimeTypeAwareAdapterInterface
{

    protected string $basePathname;

    public function __construct(
        string|Stringable|PathnameInterface $basePath = null
    )
    {
        $basePath = FileSystemUtility::normalizePathname((string) $basePath);

        if(empty($basePath)) {
            throw new InvalidArgumentException(sprintf('A non-empty pathname must be passed to the "%s" constructor.', __CLASS__));
        }

        if(!is_dir($basePath)) {
            throw new InvalidArgumentException(sprintf('The pathname passed to the "%s" constructor must reference a directory: the supplied pathname, "%s", does not reference a directory.', __CLASS__, $basePath));
        }

        $this->basePathname = $basePath . '/';
    }

    public function getBasePathname(): string
    {
        return $this->basePathname;
    }

    public function isFile(PathnameInterface $pathname): bool
    {
        if(!$this->exists($pathname)) {
            return false;
        }

        return is_file($this->getBasePathname() . $pathname->getLocalPathname());
    }

    public function isDirectory(PathnameInterface $pathname): bool
    {
        if(!$this->exists($pathname)) {
            return false;
        }

        return is_dir($this->getBasePathname() . $pathname->getLocalPathname());
    }

    public function setAccessTime(PathnameInterface $pathname, \DateTimeInterface $dateTime): AdapterInterface
    {
        ResourceExistenceAsserter::assertExistence($pathname);

        $self = $this;

        CallbackUtility::callSafely(
            function() use($pathname, $dateTime, $self) {
                return touch(
                    $self->getBasePathname() . $pathname->getLocalPathname(),
                    $self->getModificationTime($pathname)->getTimestamp(),
                    $dateTime->getTimestamp()
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'An error occurred while attempting to set the access time of the referenced resource, pathname "%s".',
            $pathname
        );

        return $this;
    }

    public function getAccessTime(PathnameInterface $pathname): \DateTimeInterface
    {
        ResourceExistenceAsserter::assertExistence($pathname);

        $self = $this;
        $timestamp = CallbackUtility::callSafely(
            function() use($pathname, $self) {
                return fileatime(
                    $self->getBasePathname() . $pathname->getLocalPathname()
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'The access time of the referenced resource, pathname "%s", could not be assessed.',
            $pathname
        );

        $date = new \DateTime();
        $date->setTimestamp($timestamp);

        return $date;
    }

    public function getCreationTime(PathnameInterface $pathname): \DateTimeInterface
    {
        ResourceExistenceAsserter::assertExistence($pathname);

        $self = $this;
        $timestamp = CallbackUtility::callSafely(
            function() use($pathname, $self) {
                return filectime(
                    $self->getBasePathname() . $pathname->getLocalPathname()
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'The creation time of the referenced resource, pathname "%s", could not be assessed.',
            $pathname
        );

        $date = new \DateTime();
        $date->setTimestamp($timestamp);

        return $date;
    }

    public function setModificationTime(PathnameInterface $pathname, \DateTimeInterface $dateTime): AdapterInterface
    {
        ResourceExistenceAsserter::assertExistence($pathname);

        $self = $this;

        CallbackUtility::callSafely(
            function() use($pathname, $dateTime, $self) {
                return touch(
                    $self->getBasePathname() . $pathname->getLocalPathname(),
                    $dateTime->getTimestamp(),
                    $this->getAccessTime($pathname)->getTimestamp()
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'An error occurred while attempting to set the modification time of the referenced resource, pathname "%s".',
            $pathname
        );

        return $this;
    }

    public function getModificationTime(PathnameInterface $pathname): \DateTimeInterface
    {
        ResourceExistenceAsserter::assertExistence($pathname);

        $self = $this;
        $timestamp = CallbackUtility::callSafely(
            function() use($pathname, $self) {
                return filemtime(
                    $self->getBasePathname() . $pathname->getLocalPathname()
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'The mdofication time of the referenced resource, pathname "%s", could not be assessed.',
            $pathname
        );

        $date = new \DateTime();
        $date->setTimestamp($timestamp);

        return $date;
    }

    public function touch(iterable|PathnameInterface $pathname, \DateTimeInterface $modificationTime, \DateTimeInterface $accessTime, bool $create): AdapterInterface
    {
        if(!$create) {
            ResourceExistenceAsserter::assertExistence($pathname);
        }

        $self = $this;

        CallbackUtility::callSafely(
            function() use($pathname, $modificationTime, $accessTime, $self) {
                return touch(
                    $self->getBasePathname() . $pathname->getLocalPathname(),
                    $modificationTime->getTimestamp(),
                    $accessTime->getTimestamp()
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'An error occurred while attempting to touch the referenced resource, pathname "%s".',
            $pathname
        );

        return $this;
    }

    public function getSize(PathnameInterface $pathname, bool $recursive): int
    {
        ResourceExistenceAsserter::assertExistence($pathname);

        if($this->isDirectory($pathname)) {
            if($recursive) {
                $size = 0;
                $iterator = $this->getIterator($pathname, []);

                foreach($iterator as $path) {
                    $size += $this->fileSystem
                        ->getFile($path)
                        ->getSize(true);
                }

                return $size;
            }

            return 0;
        }

        $self = $this;

        return CallbackUtility::callSafely(
            function() use($pathname, $self) {
                return filesize($self->getBasePathname() . $pathname->getLocalPathname());
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'An error occurred while attempting to assess the size of the referenced resource, pathname "%s".',
            $pathname
        );
    }

    public function setOwner(iterable|PathnameInterface $pathname, int|string $owner): AdapterInterface
    {
        // TODO: Implement setOwner() method.
    }

    public function getOwner(PathnameInterface $pathname): string|int
    {
        ResourceExistenceAsserter::assertExistence($pathname);

        $self = $this;

        return CallbackUtility::callSafely(
            function() use($pathname, $self) {
                return fileowner(
                    $self->getBasePathname() . $pathname->getLocalPathname()
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'An error occurred while attempting to assess the owner of the referenced pathname, "%s".',
            $pathname
        );
    }



}