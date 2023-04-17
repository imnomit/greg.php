<?php

namespace nomit\Drive\Stream;

use nomit\Drive\Exception\InvalidArgumentException;
use nomit\Drive\FileSystemInterface;
use nomit\Drive\Iterator\FileSystemIterator;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Resource\ResourceInterface;
use nomit\Drive\Stream\StreamInterface;
use nomit\Stream\StreamMode;

class StreamWrapper implements StreamWrapperInterface
{

    public $context;

    protected ?object $url = null;

    protected ?FileSystemInterface $fileSystem = null;

    protected ?FileInterface $file = null;

    protected ?FileSystemIterator $directoryIterator = null;

    protected ?StreamInterface $stream = null;

    protected function openFile(string $url): FileInterface
    {
        $this->url = (object) array_merge(
            [
                'scheme'   => 'nomitfs', // e.g. http
                'host'     => '',
                'port'     => '',
                'user'     => '',
                'pass'     => '',
                'path'     => '',
                'query'    => '', // after the question mark ?
                'fragment' => '', // after the hashmark #
            ],
            parse_url($url)
        );

        if($this->url->scheme === 'nomitfs-streams') {
            $stream = StreamManager::searchStream($this->url->host);

            if(!$stream) {
                throw new InvalidArgumentException(sprintf('The specified host, "%s", has not been registered with the specified scheme, "%s", in the "%s" stream manager.', $this->url->host, $this->url->scheme, StreamManager::class));
            }

            $this->file = $stream->getFile();
            $this->stream = $stream;

            return $this->file;
        }

        $host = $this->url->host;

        if($this->url->port !== '') {
            $host .= ':' . $this->url->port;
        }

        $this->fileSystem = StreamManager::search($host, $this->url->scheme);
        $this->file = $this->fileSystem->getFile($this->url->path);

        return $this->file;
    }

    public function makeDirectory(string $path, int $mode, int $options): bool
    {
        $this->openFile($path);

        $this->file->createDirectory($options & STREAM_MKDIR_RECURSIVE);

        return true;
    }

    public function rename(string $sourcePathname, string $destinationPathname): bool
    {
        $source = $this->openFile($sourcePathname);
        $destination = $this->openFile($destinationPathname);

        $source->moveTo($destination);

        return true;
    }

    public function deleteDirectory(string $pathname, int $options): bool
    {
        $this->openFile($pathname);

        $this->file->delete($options & STREAM_MKDIR_RECURSIVE);

        return true;
    }

    public function deleteFile(string $pathname): bool
    {
        $this->openFile($pathname);

        $this->file->delete();

        return true;
    }

    public function openDirectory(string $pathname, int $options): bool
    {
        $this->openFile($pathname);

        if($this->file->isDirectory()) {
            $this->directoryIterator = $this->file->getIterator();

            return true;
        }

        return false;
    }

    public function closeDirectory(): bool
    {
        unset($this->directoryIterator);

        return true;
    }

    public function readDirectory(): string|bool
    {
        $this->directoryIterator->next();

        if($this->directoryIterator->valid()) {
            return $this->directoryIterator->current()->getBasename();
        }

        return false;
    }

    public function rewindDirectory(): bool
    {
        $this->directoryIterator->rewind();

        return true;
    }

    public function open(string $pathname, int $mode, int $options, string &$openedPathname): bool
    {
        $this->openFile($pathname);

        if(!$this->stream) {
            $this->stream = $this->file->getStream(new StreamMode($mode));

            if($options & STREAM_USE_PATH) {
                $openedPathname = $pathname;
            }

            return true;
        }

        return true;
    }

    public function close(): void
    {
        $this->stream->close();

        unset($this->stream);
    }

    public function cast(int $type)
    {
        return $this->stream->getResource();
    }

    public function getMetadata(): array
    {
        return $this->file->getMetadata();
    }

    public function getUrlMetadata(string $url, int $flags): false|array
    {
        try {
            $this->openFile($url);

            if($this->file->isLink()
                && !($flags & STREAM_URL_STAT_LINK)
            ) {
                return $this->file->getLinkTarget()
                    ->getMetadata();
            }

            return $this->file->getMetadata();
        } catch(\Throwable $exception) {
            if($flags & STREAM_URL_STAT_QUIET) {
                throw $exception;
            }

            return false;
        }
    }

    public function lock(mixed $operation): bool
    {
        return $this->stream->lock($operation);
    }

    public function getStreamMetadata(string $path, int $option, int $variable): bool
    {
        $this->openFile($path);

        switch($option) {
            case STREAM_META_TOUCH:
                $this->file->touch(
                    $variable[0] ?? null,
                    $variable[1] ?? null
                );

                return true;

            case STREAM_META_OWNER_NAME:
            case STREAM_META_OWNER:
                $this->file->setOwner($variable);

                return true;

            case STREAM_META_GROUP_NAME:
            case STREAM_META_GROUP:
                $this->file->setGroup($variable);

                return true;

            case STREAM_META_ACCESS:
                $this->file->setMode($variable);

                return true;
        }

        return false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): bool
    {
        return $this->stream->seek($offset, $whence);
    }

    public function tell(): int
    {
        return $this->stream->tell();
    }

    public function eof(): bool
    {
        return $this->stream->eof();
    }

    public function truncate(int $size): bool
    {
        return $this->stream->truncate($size);
    }

    public function read(int $count): string
    {
        return $this->stream->read($count);
    }

    public function write(string $data): int
    {
        return $this->stream->write($data);
    }

    public function flush(): bool
    {
        return $this->stream->flush();
    }

}