<?php

namespace nomit\Messenger\Driver;

use nomit\FileSystem\FileSystem;
use nomit\Messenger\Driver\FileSystem\Exception\InsufficientPermissionsException;
use nomit\Messenger\Exception\InvalidArgumentException;

class FileSystemDriver implements DriverInterface
{

    protected string $directory;

    protected int $permissions;

    protected FileSystem $system;

    public function __construct(string $directory, int $permissions = 0740)
    {
        if(!is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('The supplied path name, "%s", references a non-existent or non-directory entity.', $directory));
        }

        if(!is_readable($directory)) {
            throw new InvalidArgumentException(sprintf('The supplied directory path name, "%s", references an unreadable directory.', $directory));
        }

        $this->directory = $directory;
        $this->permissions = $permissions;
        $this->system = new FileSystem();
    }

    public function listQueues(): array
    {
        $iterator = new \FilesystemIterator($this->directory, \FilesystemIterator::SKIP_DOTS);

        $queues = [];

        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                continue;
            }

            $queues[] = $file->getBasename();
        }

        return $queues;
    }

    public function createQueue(string $queueName): bool
    {
        $directory = $this->getQueueDirectory($queueName);

        if (is_dir($directory)) {
            return true;
        }

        return $this->system->makeDirectory($directory, 0755, true);
    }

    public function count(string $queueName): int
    {
        $iterator = new \RecursiveDirectoryIterator(
            $this->getQueueDirectory($queueName),
            \FilesystemIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator($iterator);
        $iterator = new \RegexIterator($iterator, '#\.job$#');

        return iterator_count($iterator);
    }

    public function push(string $queueName, string $message): bool
    {
        $directory = $this->getQueueDirectory($queueName);
        $file = $this->getJobFilename($queueName);

        $this->system->write($directory . DIRECTORY_SEPARATOR . $file, $message);
        $this->system->changeMode($directory . DIRECTORY_SEPARATOR . $file, $this->permissions);

        return true;
    }

    public function pop(string $queueName, int $duration = 5): array
    {
        $runtime = microtime(true) + $duration;
        $queueDir = $this->getQueueDirectory($queueName);

        $files = $this->getJobFiles($queueName);

        while (microtime(true) < $runtime) {
            if ($files) {
                $id = array_pop($files);
                if (@rename($queueDir.DIRECTORY_SEPARATOR.$id, $queueDir.DIRECTORY_SEPARATOR.$id.'.proceed')) {
                    return [file_get_contents($queueDir.DIRECTORY_SEPARATOR.$id.'.proceed'), $id];
                }

                return $this->processOrFail($queueDir, $id);
            }

            // In order to notice that a new message received, update the list.
            $files = $this->getJobFiles($queueName);

            usleep(1000);
        }

        return [null, null];
    }

    protected function processOrFail(string $directory, string $id): array
    {
        $name = $directory . DIRECTORY_SEPARATOR . $id;
        $newName = $name . '.proceed';

        if(!$this->system->rename($name, $newName)) {
            throw new InsufficientPermissionsException(sprintf('An error occurred while attempting to process the configured file, path "%s".', $newName));
        }

        return [$this->system->read($newName), $id];
    }

    public function acknowledge(string $queueName, mixed $receipt): bool
    {
        $directory = $this->getQueueDirectory($queueName);
        $path = $directory . DIRECTORY_SEPARATOR . $receipt . '.proceed';

        if (!is_file($path)) {
            return false;
        }

        $this->system->remove($path);

        return true;
    }

    public function peekQueue(string $queueName, int $index = 0, int $limit = null): array
    {
        $directory = $this->getQueueDirectory($queueName);

        $iterator = new \GlobIterator($directory.DIRECTORY_SEPARATOR.'*.job', \FilesystemIterator::KEY_AS_FILENAME);
        $files = array_keys(iterator_to_array($iterator));

        natsort($files);

        $files = array_reverse($files);

        if($limit !== null) {
            $files = array_slice($files, $index, $limit);
        }

        $messages = [];

        foreach ($files as $file) {
            $messages[] = $this->system->read($directory . DIRECTORY_SEPARATOR . $file);
        }

        return $messages;
    }

    public function removeQueue(string $queueName): bool
    {
        $iterator = new \RecursiveDirectoryIterator(
            $this->getQueueDirectory($queueName),
            \FilesystemIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator($iterator);
        $iterator = new \RegexIterator($iterator, '#\.job(.proceed)?$#');

        foreach ($iterator as $file) {
            /* @var $file \DirectoryIterator */
            $this->system->remove($file->getRealPath());
        }

        $this->system->remove($this->getQueueDirectory($queueName));

        return true;
    }

    public function inform(): array
    {
        return [];
    }

    protected function getQueueDirectory(string $queueName): string
    {
        return $this->directory . DIRECTORY_SEPARATOR . str_replace(['\\', '.'], '-', $queueName);
    }

    protected function getJobFilename(string $queueName): string
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . 'nomit.messenger.meta';

        if (!is_file($path)) {
            touch($path);
            chmod($path, $this->permissions);
        }

        $file = new \SplFileObject($path, 'r+');
        $file->flock(LOCK_EX);

        $meta = unserialize($file->fgets());

        $id = isset($meta[$queueName]) ? $meta[$queueName] : 0;

        ++$id;

        $filename = sprintf('%d.job', $id);
        $meta[$queueName] = $id;

        $content = serialize($meta);

        $file->fseek(0);
        $file->fwrite($content, strlen($content));
        $file->flock(LOCK_UN);

        return $filename;
    }

    protected function getJobFiles(string $queueName): array
    {
        $iterator = new \GlobIterator(
            $this->getQueueDirectory($queueName) . DIRECTORY_SEPARATOR . '*.job',
            \FilesystemIterator::KEY_AS_FILENAME
        );

        $files = array_keys(iterator_to_array($iterator));

        natsort($files);

        return $files;
    }

}