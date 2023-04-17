<?php

namespace nomit\Console\Queue\Persistence;

use nomit\Console\Command\CommandInterface;
use nomit\Console\Serialization\SerializerInterface;
use nomit\FileSystem\Directory\Directory;
use nomit\FileSystem\FileSystem;
use nomit\Notification\Exception\CreateQueueException;
use Psr\Log\LoggerInterface;

final class FileSystemPersistence implements PersistenceInterface
{

    public function __construct(
        private FileSystem $fileSystem,
        private string $directory,
        private ?LoggerInterface $logger = null
    )
    {
    }

    public function createQueue(string $queueName): bool
    {
        if($this->hasQueue($queueName)) {
            return true;
        }

        $pathname = $this->getQueueDirectoryPathname($queueName);

        try {
            $this->fileSystem->makeDirectory($pathname);
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to create a command queue for the queue with the name {name} at the path {path}: {message}.', [
                'name' => $queueName,
                'path' => $pathname,
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }

        $this->logger?->debug('A command queue for the queue with the name {name} has been successfully created at the path {path}.', [
            'name' => $queueName,
            'path' => $pathname
        ]);

        return true;
    }

    public function hasQueue(string $queueName): bool
    {
        return FileSystem::isDirectory($this->getQueueDirectoryPathname($queueName));
    }

    public function removeQueue(string $queueName): bool
    {
        if(!$this->hasQueue($queueName)) {
            return true;
        }

        $pathname = $this->getQueueDirectoryPathname($queueName);

        try {
            $this->fileSystem->remove($pathname);
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to remove the directory for the command queue with the name {name} at the path {path}: {message}.', [
                'name' => $queueName,
                'path' => $pathname,
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }

        $this->logger?->debug('The directory, path {path}, for the queue named {name} has been successfully removed.', [
            'name' => $queueName,
            'path' => $pathname
        ]);

        return true;
    }

    public function push(string $queueName, string $commandName, string $command): bool
    {
        if(!$this->hasQueue($queueName) && !$this->createQueue($queueName)) {
            throw new CreateQueueException(sprintf('An error occurred while attempting to create the command queue for the queue named "%s".', $queueName));
        }

        $pathname = $this->getCommandFilePathname($queueName, $commandName);

        if(FileSystem::isFile($pathname)) {
            return true;
        }

        try {
            $this->fileSystem->write($pathname, $command);
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to write the file, path {path}, for the supplied command job: {message}.', [
                'path' => $pathname,
                'message' => $exception->getMessage(),
                'exception' => $exception,
                'command' => $command
            ]);

            return false;
        }

        $this->logger?->debug('A file, path {path}, has been successfully created for the supplied command job.', [
            'path' => $pathname,
            'command' => $command
        ]);

        return true;
    }

    public function pop(string $queueName, int $duration = 5): ?array
    {
        if(!$this->hasQueue($queueName) && !$this->createQueue($queueName)) {
            throw new CreateQueueException(sprintf('An error occurred while attempting to create the command queue for the queue named "%s".', $queueName));
        }

        $startTime = microtime(true);

        do {
            $runtime = $startTime - microtime(true);
            $directoryPathname = $this->getQueueDirectoryPathname($queueName);
            $directory = new Directory($directoryPathname);

            foreach($directory as $file) {
                if($file->isDot() || $file->isLink() || !$file->isFile()) {
                    continue;
                }

                $result = [$this->fileSystem->read($file->getPathname()), $file->getFilename()];

                $this->acknowledge($queueName, $result[1]);

                return $result;
            }
        } while($this->count($queueName) > 0 && ($runtime < $duration));

        return [null, null];
    }

    private function count(string $queueName): int
    {
        if(!$this->hasQueue($queueName) && !$this->createQueue($queueName)) {
            throw new CreateQueueException(sprintf('An error occurred while attempting to create the command queue for the queue named "%s".', $queueName));
        }

        $directory = $this->getQueueDirectoryPathname($queueName);
        $directory = new Directory($directory);

        return iterator_count($directory);
    }

    public function acknowledge(string $queueName, mixed $receipt): bool
    {
        if(!$this->hasQueue($queueName) && !$this->createQueue($queueName)) {
            throw new CreateQueueException(sprintf('An error occurred while attempting to create the command queue for the queue named "%s".', $queueName));
        }

        [$payload, $id] = $receipt;
        $pathname = $this->getCommandFilePathname($queueName, $id, false);

        try {
            $this->fileSystem->remove($pathname);
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to acknowledge the command with the ID {id} at the pathname {path}: {message}.', [
                'id' => $id,
                'path' => $pathname,
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }

        $this->logger?->debug('The command with the ID {id} at the path {path} has been successfully acknowledged.', [
            'id' => $id,
            'path' => $pathname
        ]);

        return true;
    }

    public function peek(string $queueName, int $index = 0, int $limit = null): array
    {
        if(!$this->hasQueue($queueName) && !$this->createQueue($queueName)) {
            throw new CreateQueueException(sprintf('An error occurred while attempting to create the command queue for the queue named "%s".', $queueName));
        }

        $commands = [];

        $directoryPathname = $this->getQueueDirectoryPathname($queueName);
        $directory = new Directory($directoryPathname);

        foreach($directory as $file) {
            $commands[] = $this->fileSystem->read($file->getPathname());
        }

        if($index !== 0 && $limit !== null) {
            $commands = array_slice($commands, $index, $limit);
        }

        return $commands;
    }

    private function getQueueDirectoryPathname(string $queueName): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . md5($queueName) . DIRECTORY_SEPARATOR;
    }

    private function getCommandFilePathname(string $queueName, string $commandId, bool $hash = true): string
    {
        return $this->getQueueDirectoryPathname($queueName) . $hash ? md5($commandId) : $commandId . '.job';
    }

}