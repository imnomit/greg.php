<?php

namespace nomit\Drive\Stream;

use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Stream\AbstractObservableStream;
use nomit\Stream\StreamFactory;
use nomit\Stream\StreamInterface;
use nomit\Stream\StreamModeInterface;
use nomit\Utility\Concern\Stringable;

class LocalStream extends AbstractObservableStream implements StreamInterface
{

    protected ?\nomit\Stream\StreamInterface $selfResource = null;

    protected ?int $streamIndex = null;

    public function __construct(
        protected string $url,
        protected PathnameInterface $pathname,
        StreamModeInterface $mode = null
    )
    {
        parent::__construct($this->url, $mode?->getMode() ?? 'r');
    }

    public function getFile(): FileInterface
    {
        return $this->pathname
            ->getAdapter()
            ->getFileSystem()
            ->getFile($this->pathname);
    }

    public function getResource()
    {
        if($this->streamIndex === null) {
            $this->streamIndex = StreamManager::registerStream($this);
        }

        return ($this->selfResource = (new StreamFactory())->createStream('nomitfs-streams://' . $this->streamIndex, $this->mode))
            ->getResource();
    }

    protected function setStream($stream, StreamModeInterface|string|Stringable $mode = 'rb'): void
    {
        parent::setStream($stream, $mode);

        $this->hookOpened($mode);
    }

    public function close(): void
    {
        $this->selfResource?->close();

        parent::close();
    }

}