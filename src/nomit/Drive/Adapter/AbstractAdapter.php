<?php

namespace nomit\Drive\Adapter;

use nomit\Drive\Exception\Adapter\AdapterException;
use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Exception\OperationException;
use nomit\Drive\Exception\Resource\DirectoryException;
use nomit\Drive\Exception\Resource\OverwriteDirectoryWithDirectoryException;
use nomit\Drive\Exception\Resource\OverwriteDirectoryWithFileException;
use nomit\Drive\Exception\Resource\OverwriteFileOperationException;
use nomit\Drive\Exception\UnexpectedValueException;
use nomit\Drive\FileSystemInterface;
use nomit\Drive\Iterator\PathnameIterator;
use nomit\Drive\Iterator\RecursivePathnameIterator;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Utility\ListModeEnumeration;
use nomit\Drive\Utility\OperationEnumeration;
use nomit\Exception\Exception;
use nomit\FileSystem\Exception\IOException;
use nomit\Stream\StreamMode;
use nomit\Utility\Bitmask\Bitmask;
use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Bitmask\BitmaskUtility;
use nomit\Utility\Callback\CallbackUtility;

abstract class AbstractAdapter implements AdapterInterface
{

    protected static ?array $lastError = null;

    protected ?FileSystemInterface $fileSystem = null;

    protected ?AdapterInterface $primaryAdapter = null;

    protected AdapterInterface|null $parentAdapter;

    public static function handleError(string $type, string $message): void
    {
        self::$lastError = [$type, $message];
    }

    public static function getErrorType(): ?string
    {
        return self::$lastError
            ? self::$lastError[0]
            : null;
    }

    public static function getErrorMessage(): ?string
    {
        return self::$lastError
            ? self::$lastError[1]
            : null;
    }

    public static function call(callable $callback, ...$arguments): mixed
    {
        self::$lastError = null;

        set_error_handler(__CLASS__ . '::handleError');

        try {
            $result = $callback(...$arguments);

            restore_error_handler();

            return $result;
        } catch(\Throwable $exception) {
        }

        restore_error_handler();

        throw $exception;
    }

    private static function linkException(string $origin, string $target, string $linkType): void
    {
        if (self::$lastError
            && '\\' === DIRECTORY_SEPARATOR
            && str_contains(self::$lastError, 'error code(1314)')
        ) {
            throw new Exception(
                sprintf('Unable to create "%s" link due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?', $linkType),
            );
        }

        throw new Exception(
            sprintf('Failed to create "%s" link from "%s" to "%s": "%s".', $linkType, $origin, $target, self::$lastError)
        );
    }

    public function setFileSystem(?FileSystemInterface $fileSystem): AdapterInterface
    {
        $this->fileSystem = $fileSystem;
        $this->primaryAdapter = $fileSystem?->getPrimaryAdapter();

        return $this;
    }

    public function getFileSystem(): FileSystemInterface
    {
        return $this->fileSystem;
    }

    public function getPrimaryAdapter(): AdapterInterface
    {
        return $this->primaryAdapter;
    }

    public function setParentAdapter(AdapterInterface $adapter = null): AdapterInterface
    {
        $this->parentAdapter = $adapter;

        return $this;
    }

    public function getParentAdapter(): AdapterInterface|null
    {
        return $this->parentAdapter;
    }

    public function resolveLocalPathname(PathnameInterface $pathname, AdapterInterface &$localAdapter, string &$localAdapterPathname): AdapterInterface
    {
        $localAdapter = $this;
        $localAdapterPathname = $pathname->getPathname();

        return $this;
    }

    public function copyTo(PathnameInterface $sourcePathname, PathnameInterface $destinationPathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        return $destinationPathname->getLocalAdapter()->copyFrom(
            $destinationPathname,
            $sourcePathname,
            $flags
        );
    }

    public function copyFrom(PathnameInterface $destinationPathname, PathnameInterface $sourcePathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        $destinationPathname = $destinationPathname->getParent();
        $flags = new Bitmask($flags);

        if($flags->has(OperationEnumeration::OPERATION_PARENTS)) {
            $destinationPathname->getLocalAdapter()->createDirectory(
                $destinationPathname,
                true
            );
        } else if(!$destinationPathname->getLocalAdapter()->isDirectory($destinationPathname)) {
            throw new DirectoryException($destinationPathnameString = $destinationPathname->getPathname(), sprintf('The supplied directory pathname, "%s", references a non-directory resource.', $destinationPathnameString));
        }

        $destinationExists = $this->exists($destinationPathname);
        $sourceIsDirectory = $sourcePathname->getLocalAdapter()->isDirectory($sourcePathname);
        $destinationIsDirectory = $this->isDirectory($destinationPathname);

        if(!$destinationExists) {
            if($sourceIsDirectory) {
                $destinationIsDirectory = true;
            } else {
                $destinationIsDirectory = false;
            }
        } else if(!$sourceIsDirectory && $destinationIsDirectory) {
            if(!$flags->has(OperationEnumeration::OPERATION_REJECT)
                && $flags->has(OperationEnumeration::OPERATION_REPLACE)
            ) {
                $this->delete($destinationPathname, true, false);

                $destinationIsDirectory = false;
            } else if($flags->has(OperationEnumeration::OPERATION_MERGE)) {
                $destinationInsidePathname = $destinationPathname->getChild($sourcePathname);

                $sourcePathname->getLocalAdapter()->copyTo(
                    $sourcePathname,
                    $destinationInsidePathname,
                    $flags
                );

                return $this;
            } else {
                throw new OverwriteDirectoryWithFileException(
                    $sourcePathname,
                    $destinationPathname
                );
            }
        } else if($sourceIsDirectory && !$destinationIsDirectory) {
            if(!$flags->has(OperationEnumeration::OPERATION_REJECT)
                && $flags->has(OperationEnumeration::OPERATION_REPLACE)
            ) {
                $this->delete($destinationPathname, false, false);

                $this->createDirectory($destinationPathname, false);

                $destinationIsDirectory = true;
            } else {
                throw new OverwriteDirectoryWithFileException(
                    $sourcePathname,
                    $destinationPathname
                );
            }
        }

        if($sourceIsDirectory && $destinationIsDirectory) {
            if(!$flags->has(OperationEnumeration::OPERATION_REJECT)
                && $flags->has(OperationEnumeration::OPERATION_REPLACE)
            ) {
                if($destinationExists) {
                    $this->delete($destinationPathname, true, false);
                }

                $this->createDirectory($destinationPathname, false);

                $flags->add(OperationEnumeration::OPERATION_RECURSIVE);
            }

            if($flags->has(OperationEnumeration::OPERATION_RECURSIVE)) {
                $iterator = $sourcePathname->getLocalAdapter()->getIterator($sourcePathname, []);

                foreach($iterator as $sourceChildPathname) {
                    $sourcePathname->getLocalAdapter()->getPrimaryAdapter()->copyTo(
                        $sourceChildPathname,
                        $destinationPathname->getChild($sourceChildPathname),
                        $flags
                    );
                }
            } else {
                throw new OverwriteDirectoryWithFileException(
                    $sourcePathname,
                    $destinationPathname
                );
            }
        } else if(!$sourceIsDirectory && !$destinationIsDirectory) {
            if(!$flags->has(OperationEnumeration::OPERATION_REJECT)
                && $flags->has(OperationEnumeration::OPERATION_REPLACE)
            ) {
                $destinationClass = new \ReflectionClass($this);
                $sourceClass = new \ReflectionClass($sourcePathname->getLocalAdapter());

                if($destinationClass->getName() === $sourceClass->getName()
                    || $destinationClass->isSubclassOf($sourceClass)
                    || $sourceClass->isSubclassOf($destinationClass)
                ) {
                    if($this->copyNatively($sourceClass, $destinationClass)) {
                        return $this;
                    }
                }

                return CallbackUtility::callSafely(
                    function() use($sourcePathname, $destinationPathname) {
                        $sourceStream = $sourcePathname->getLocalAdapter()->getStream($sourcePathname, new StreamMode('rb'));
                        $destinationStream = $this->getStream($destinationPathname, new StreamMode('wb'));

                        $result = $sourceStream->copyToStream($destinationStream);

                        $sourceStream->close();
                        $destinationStream->close();

                        return $result;
                    },
                    [],
                    OperationException::class,
                    ExceptionCodeEnumeration::OPERATION_EXCEPTION,
                    'An error occurred while attempting to copy the stream for the source file, filename "%s", to the stream for the destination file, filename "%s".',
                    $sourcePathname,
                    $destinationPathname
                );
            }

            throw new OverwriteFileOperationException(
                $sourcePathname,
                $destinationPathname
            );
        } else {
            throw new UnexpectedValueException(sprintf('The state of either or both the source file, filename "%s", and/or destination file, filename "%s", is/are illegal.', $sourcePathname->getPathname(), $destinationPathname->getPathname()), ExceptionCodeEnumeration::FILESYSTEM_EXCEPTION);
        }

        return $this;
    }

    public function copyNatively(
        PathnameInterface $sourcePathname,
        PathnameInterface $destinationPathname
    ): bool
    {
        return false;
    }

    public function moveTo(PathnameInterface $sourcePathname, PathnameInterface $destinationPathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        $destinationPathname->getLocalAdapter()->moveFrom(
            $destinationPathname,
            $sourcePathname,
            $flags
        );

        return $this;
    }

    public function moveFrom(PathnameInterface $destinationPathname, PathnameInterface $sourcePathname, OperationEnumeration|BitmaskInterface|int $flags): AdapterInterface
    {
        $flags = new Bitmask($flags);
        $destinationParentPathname = $destinationPathname->getParent();

        if($flags->has(OperationEnumeration::OPERATION_PARENTS)) {
            $destinationParentPathname->getLocalAdapter()->createDirectory(
                $destinationParentPathname,
                true
            );
        } else if(!$destinationParentPathname->getLocalAdapter()->isDirectory($destinationParentPathname)) {
            throw new DirectoryException(
                $destinationParentPathname->getPathname(),
                sprintf('The parent, "%s", of the file referenced by the supplied destination filename, "%s", is not a directory.', $destinationParentPathname->getParent(), $destinationPathname->getPathname())
            );
        }

        $destinationExists = $this->exists($destinationPathname);
        $sourceIsDirectory = $sourcePathname->getLocalAdapter()->isDirectory($sourcePathname);
        $destinationIsDirectory = $this->isDirectory($destinationPathname);

        if(!$destinationExists) {
            if($sourceIsDirectory) {
                $destinationIsDirectory = true;
            } else {
                $destinationIsDirectory = false;
            }
        } else if(!$sourceIsDirectory
            && $destinationIsDirectory
        ) {
            if(!$flags->has(OperationEnumeration::OPERATION_REJECT)
                && $flags->has(OperationEnumeration::OPERATION_REPLACE)
            ) {
                $this->delete($destinationPathname, true, false);

                $destinationIsDirectory = false;
            } else if($flags->has(OperationEnumeration::OPERATION_MERGE)) {
                $destinationInsidePathname = $destinationPathname->getChild($sourcePathname);

                $sourcePathname->getLocalAdapter()->moveTo(
                    $sourcePathname,
                    $destinationInsidePathname,
                    $flags
                );

                return $this;
            } else {
                throw new OverwriteDirectoryWithFileException(
                    $sourcePathname,
                    $destinationPathname
                );
            }
        } else if($sourceIsDirectory
            && !$destinationIsDirectory
        ) {
            if(!$flags->has(OperationEnumeration::OPERATION_REJECT)
                && $flags->has(OperationEnumeration::OPERATION_REPLACE)
            ) {
                $this->delete($destinationPathname, false, false);

                $this->createDirectory($destinationPathname, false);

                $destinationIsDirectory = true;
            } else {
                throw new OverwriteDirectoryWithFileException(
                    $sourcePathname,
                    $destinationPathname
                );
            }
        }

        if($sourceIsDirectory
            && $destinationIsDirectory
        ) {
            if(!$destinationExists
                || (!$flags->has(OperationEnumeration::OPERATION_REJECT) && $flags->has(OperationEnumeration::OPERATION_REPLACE))
            ) {
                if($destinationExists) {
                    $this->delete($destinationPathname, true, false);
                }

                $flags->add(OperationEnumeration::OPERATION_RECURSIVE);
            }

            if($flags->has(OperationEnumeration::OPERATION_RECURSIVE)) {
                $destinationClass = new \ReflectionClass($this);
                $sourceClass = new \ReflectionClass($sourcePathname->getLocalAdapter());

                if($destinationClass->getName() === $sourceClass->getName()
                    || $destinationClass->isSubclassOf($sourceClass)
                    || $sourceClass->isSubclassOf($destinationClass)
                ) {
                    if($this->nativelyMove($sourcePathname, $destinationPathname)) {
                        return $this;
                    }
                }

                $iterator = $sourcePathname->getLocalAdapter()->getIterator($sourcePathname, []);

                /**
                 * @var PathnameInterface $sourceChildPathname
                 */
                foreach($iterator as $sourceChildPathname) {
                    $sourcePathname->getLocalAdapter()
                        ->getPrimaryAdapter()
                        ->moveTo(
                            $sourceChildPathname,
                            $destinationPathname->getChild($sourceChildPathname),
                            $flags
                        );
                }
            } else {
                throw new OverwriteDirectoryWithDirectoryException(
                    $sourcePathname,
                    $destinationPathname
                );
            }
        } else if(!$sourceIsDirectory
            && !$destinationIsDirectory
        ) {
            if(!$destinationExists
                || (!$flags->has(OperationEnumeration::OPERATION_REJECT) && $flags->has(OperationEnumeration::OPERATION_REPLACE))
            ) {
                $destinationClass = new \ReflectionClass($this);
                $sourceClass = new \ReflectionClass($sourcePathname->getLocalAdapter());

                if($destinationClass->getName() === $sourceClass->getName()
                    || $destinationClass->isSubclassOf($sourceClass)
                    || $sourceClass->isSubclassOf($destinationClass)
                ) {
                    if($this->moveNatively($sourcePathname, $destinationPathname)) {
                        return $this;
                    }
                }

                return CallbackUtility::callSafely(
                    function() use($sourcePathname, $destinationPathname) {
                        $sourceStream = $sourcePathname->getLocalAdapter()
                            ->getStream($sourcePathname, new StreamMode('rb'));

                        $destinationStream = $this->getStream($destinationPathname, new StreamMode('wb'));

                        $result = $sourceStream->copyToStream($destinationStream);

                        $sourceStream->close();
                        $destinationStream->close();

                        return $result;
                    },
                    [],
                    OperationException::class,
                    ExceptionCodeEnumeration::OPERATION_EXCEPTION,
                    'An error occurred while attempting to move the resource referenced by the supplied source pathname, "%s", to the resource referenced by the supplied destination pathname, "%s".',
                    $sourcePathname->getPathname(),
                    $destinationPathname->getPathname(),
                );
            }
        } else {
            throw new UnexpectedValueException(sprintf('The state of either or both the source resource, pathname "%s", and/or destination resource, pathname "%s", is/are illegal.', $sourcePathname->getPathname(), $destinationPathname->getPathname()), ExceptionCodeEnumeration::FILESYSTEM_EXCEPTION);
        }

        return $this;
    }

    public function nativelyMove(
        PathnameInterface $sourcePathname,
        PathnameInterface $destinationPathname
    ): bool
    {
        return false;
    }

    public function count(PathnameInterface $pathname, array $filters): int
    {
        return iterator_count(
            $this->getIterator($pathname, $filters)
        );
    }

    public function getIterator(PathnameInterface $pathname, array $filters): \Iterator
    {
        if(BitmaskUtility::has(ListModeEnumeration::LIST_RECURSIVE, $filters)) {
            return new \RecursiveIteratorIterator(
                new RecursivePathnameIterator($pathname, $filters)
            );
        }

        return new PathnameIterator($pathname, $filters);
    }

}