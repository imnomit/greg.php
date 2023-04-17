<?php

namespace nomit\Stream;

use function nomit\copy_to_string;

abstract class AbstractStreamDecorator implements StreamInterface
{

    protected StreamInterface $stream;

    /**
     * @param StreamInterface|null $stream Stream to decorate
     */
    public function __construct(StreamInterface $stream = null)
    {
        if ($stream) {
            $this->stream = $stream;
        }
    }

    /**
     * Magic method used to create a new stream if streams are not added in
     * the constructor of a decorator (e.g., LazyOpenStream).
     *
     * @param string $name Name of the property (allows "stream" only).
     *
     * @return StreamInterface
     */
    public function __get($name)
    {
        if ($name === 'stream') {
            $this->stream = $this->createStream();

            return $this->stream;
        }

        throw new \UnexpectedValueException("$name not found on class");
    }

    /**
     * Allow decorators to implement custom methods
     *
     * @param string $method Missing method name
     * @param array  $args   Method arguments
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        $result = call_user_func_array(array($this->stream, $method), $args);

        // Always return the wrapped object if the result is a return $this
        return $result === $this->stream ? $this : $result;
    }

    public function getContents()
    {
        return copy_to_string($this);
    }

    public function close()
    {
        $this->stream->close();
    }

    public function getMetadata($key = null)
    {
        return $this->stream->getMetadata($key);
    }

    public function detach()
    {
        return $this->stream->detach();
    }

    public function getSize()
    {
        return $this->stream->getSize();
    }

    public function eof()
    {
        return $this->stream->eof();
    }

    public function tell()
    {
        return $this->stream->tell();
    }

    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    public function isWritable()
    {
        return $this->stream->isWritable();
    }

    public function isSeekable()
    {
        return $this->stream->isSeekable();
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        $this->stream->seek($offset, $whence);
    }

    public function read($length)
    {
        return $this->stream->read($length);
    }

    public function write($string)
    {
        return $this->stream->write($string);
    }

    public function toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}
