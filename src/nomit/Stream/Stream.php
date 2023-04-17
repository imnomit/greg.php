<?php

namespace nomit\Stream;

use nomit\Stream\Exception\ExceptionInterface;
use nomit\Stream\Exception\InvalidArgumentException;
use nomit\Stream\Exception\LogicException;
use nomit\Stream\Exception\RuntimeException;
use nomit\Utility\Arrays;
use nomit\Utility\Concern\Stringable;

class Stream implements StreamInterface
{

    private const ALLOWED_RESOURCE_TYPES = [
        'gd',
        'stream'
    ];

    protected StreamModeInterface $mode;

    protected $resource;

    protected int $buffer_length = 4096;

    protected ?int $lock = null;

    protected ?array $metadata = null;

    public function __construct($stream, string|StreamModeInterface|Stringable $mode = 'r')
    {
        $this->setStream($stream, $mode);
    }

    public function setBuffer(int $buffer): bool
    {
        $out = 0 === stream_set_write_buffer($this->resource, $buffer);

        if (true === $out) {
            $this->buffer_length = $buffer;
        }

        return $out;
    }

    public function unbuffer(): bool
    {
        return $this->setBuffer(0);
    }

    public function setBufferLength(int $length): self
    {
        $this->buffer_length = $length;

        return $this;
    }

    public function getBufferLength(): int
    {
        return $this->buffer_length;
    }

    protected function setStream($stream, string|Stringable|StreamModeInterface $mode = 'rb'): void
    {
        $error = null;
        $resource = $stream;

        if(is_string($stream)) {
            set_error_handler(function($exception) use (&$error) {
                if($exception !== E_WARNING) {
                    return;
                }

                $error = $exception;
            });

            $resource = fopen($stream, $mode);

            restore_error_handler();
        }

        if($error) {
            throw new RuntimeException(sprintf('The supplied stream reference, typed "%s", is invalid.', get_debug_type($stream)));
        }

        if(!$this->isValidResourceType($resource)) {
            throw new InvalidArgumentException(sprintf('The supplied stream must be a string stream identifier or stream resource: instead, a "%s"-typed value was supplied.', get_debug_type($stream)));
        }

        $this->mode = new StreamMode($mode);
        $this->resource = $resource;
    }

    public function getMode(): string
    {
        return $this->mode->getMode();
    }

    public function hasResource(): bool
    {
        return is_resource($this->resource);
    }

    public function getResource()
    {
        return $this->resource;
    }

    protected function isValidResourceType($resource): bool
    {
        if (is_resource($resource)) {
            return in_array(get_resource_type($resource), self::ALLOWED_RESOURCE_TYPES, true);
        }

        if (PHP_VERSION_ID >= 80000 && $resource instanceof \GdImage) {
            return true;
        }

        return false;
    }

    public function attach($resource, string $mode = 'r'): void
    {
        $this->setStream($resource, $mode);
    }

    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;

        return $resource;
    }

    public function getSize(): ?int
    {
        if (null === $this->resource) {
            return null;
        }

        $statistics = fstat($this->resource);

        if ($statistics !== false) {
            return $statistics['size'];
        }

        return null;
    }

    public function tell(): int
    {
        if(!$this->resource) {
            throw new LogicException(sprintf('The "%s"-classed stream could not tell because its resource is null.', __CLASS__));
        }

        $result = ftell($this->resource);

        if(!is_int($result)) {
            throw new RuntimeException(sprintf('An error occurred while "%s" attempted" to tell the current resource stream.', __CLASS__));
        }

        return $result;
    }

    public function eof(): bool
    {
        if(!$this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if(!$this->resource) {
            throw new LogicException(sprintf('The "%s" stream could not be seeked, because no resource has been sasigned to it.', __CLASS__));
        }

        if(!$this->isSeekable()) {
            throw new LogicException(sprintf('The "%s" stream is cannot be seeked because it is unseekable.', __CLASS__));
        }

        $result = fseek($this->resource, $offset, $whence);

        if(0 === $result) {
            throw new RuntimeException(sprintf('An error occurred while the "%s"-classed stream attempted to seek the assigned resource.', __CLASS__));
        }
    }

    public function isSeekable()
    {
        if(!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return $meta['seekable'];
    }

    public function rewind(int $offset = 0): void
    {
        $this->seek($offset);
    }

    public function write($string): int
    {
        if(!$this->resource) {
            throw new LogicException(sprintf('The "%s"-classed stream could not write because no stream has been asssigned to it.', __CLASS__));
        }

        if(!$this->isWritable()) {
            throw new LogicException(sprintf('The "%s"-classed stream cannot write, because it is unwritable.', __CLASS__));
        }

        $result = fwrite($this->resource, $string);

        if(false === $result) {
            throw new RuntimeException(sprintf('An error occurred while the "%s"-classed stream attempted to write to its assigned resource.', __CLASS__));
        }

        return $result;
    }

    public function isWritable(): bool
    {
        if (! $this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        $mode = $meta['mode'];

        return (
            str_contains($mode, 'x')
            || str_contains($mode, 'w')
            || str_contains($mode, 'c')
            || str_contains($mode, 'a')
            || str_contains($mode, '+')
        );
    }

    public function read($length = null, int $offset = 0): string
    {
        if(!$this->resource) {
            throw new LogicException(sprintf('The "%s"-classed stream cannot be read because no resource has been assigned to it.', __CLASS__));
        }

        if(!$this->isReadable()) {
            throw new LogicException(sprintf('The "%s"-classed stream cannot be read because it is unreadable.', __CLASS__));
        }

        $this->rewind($offset);

        if($length === null) {
            $size = $this->getSize();
            $length = $size - $offset;
        }

        if($length <= 0) {
            return '';
        }

        if(false === $result = fread($this->resource, $length)) {
            throw new RuntimeException(sprintf('An error occurred while the "%s"-classed stream attempted to read from its assigned resource.', __CLASS__));
        }

        return $result;
    }

    public function getLine(int $length = null, string $ending = "\n"): string
    {
        if(!$this->resource) {
            throw new LogicException(sprintf('The "%s"-classed stream cannot be read because no resource has been assigned to it.', __CLASS__));
        }

        if(!$this->isReadable()) {
            throw new LogicException(sprintf('The "%s"-classed stream cannot be read because it is unreadable.', __CLASS__));
        }

        $result = stream_get_line($this->resource, $length, $ending);

        if(false === $result) {
            throw new RuntimeException(sprintf('An error occurred while the "%s"-classed stream attempted to read the contents of the assigned stream.', __CLASS__));
        }

        return $result;
    }

    public function isReadable(): bool
    {
        if (! $this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        $mode = $meta['mode'];

        return (str_contains($mode, 'r') || str_contains($mode, '+'));
    }

    public function getContents(): false|string
    {
        if(!$this->isReadable()) {
            throw new LogicException(sprintf('The "%s"-classed stream cannot have its contents fetched because it is unreadable.', __CLASS__));
        }

        $result = stream_get_contents($this->resource);

        if(false === $result) {
            throw new RuntimeException(sprintf('An error occurred while the "%s"-classed stream attempted to fetch its assigned resource\'s contents.', __CLASS__));
        }

        return $result;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getMetadata($key = null, mixed $default = null): mixed
    {
        if($this->metadata === null) {
            $this->metadata = stream_get_meta_data($this->resource);
        }

        if ($key === null) {
            return $this->metadata;
        }

        return Arrays::get($this->metadata, $key, $default);
    }

    public function close(): void
    {
        if(!$this->resource) {
            return;
        }

        $resource = $this->detach();

        fclose($resource);
    }

    public function flush(): bool
    {
        return fflush($this->resource);
    }

    public function lock(int $mode): StreamInterface
    {
        if(!$this->isLocked()) {
            if(!flock($this->resource, $mode | LOCK_NB)) {
                throw new RuntimeException(sprintf('The "%s" stream handler was unable to acquire a file-lock on the wrapped resource.', get_class($this)), 500);
            }

            $this->lock = $mode;
        }

        return $this;
    }

    public function isLocked(): bool
    {
        return $this->lock !== null;
    }

    public function unlock(): StreamInterface
    {
        if($this->isLocked()) {
            flock($this->resource, LOCK_UN);
        }

        return $this;
    }

    public function getHash(string $algorithm = 'sha256', bool $raw = false): string
    {
        $this->rewind();

        $hashHandler = hash_init($algorithm);

        hash_update_stream($hashHandler, $this->resource);

        return hash_final($hashHandler, $raw);
    }

    public function copyTo($handle, int $offset = 0, int $length = null): false|int
    {
        if($length !== null) {
            $result = stream_copy_to_stream($this->resource, $handle, $offset, $length);
        } else if($offset > 0) {
            $result = stream_copy_to_stream($this->resource, $handle, $offset);
        } else {
            $result = stream_copy_to_stream($this->resource, $handle);
        }

        if($result === false) {
            throw new RuntimeException(sprintf('An error occurred while attempting to copy the contents of the stream represented by "%s" to the supplied resource handle.', get_class($this)), 500);
        }

        return $result;
    }

    public function copyToStream(StreamInterface $stream, int $offset = 0, ?int $length = null): false|int
    {
        return $this->copyTo($stream->getResource(), $offset, $length);
    }

    public function passthru(int $offset = 0, ?int $length = null, int $bufferSize = 1024): void
    {
        if($length === null) {
            if(fpassthru($this->resource) !== false) {
                $this->flush();

                return;
            }

            throw new RuntimeException(sprintf('An error occurred while attempting to read from the stream assigned to "%s".', get_class($this)), 500);
        }

        $remaining = $length;

        while($remaining > 0 && !$this->eof()) {
            $readLength = ($remaining > $bufferSize) ? $bufferSize : $remaining;
            $remaining -= $readLength;

            try {
                echo $this->read($readLength);

                $this->flush();
            } catch(ExceptionInterface $exception) {
                throw new RuntimeException(sprintf('An error occurred while attempting to read from the stream assigned to "%s".', get_class($this)), 500, $exception);
            }
        }
    }

    public function truncate(int $size): bool
    {
        return ftruncate($this->resource, $size);
    }

    public function toString(): string
    {
        if (! $this->isReadable()) {
            return '';
        }

        try {
            if ($this->isSeekable()) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}