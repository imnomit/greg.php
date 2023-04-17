<?php

namespace nomit\Stream;

/**
 * Converts casper streams into PHP stream resources.
 */
class StreamWrapper implements StreamWrapperInterface
{
    /** @var resource */
    public $context;

    /** @var StreamInterface */
    private $stream;

    /** @var string r, r+, or w */
    private $mode;

    /**
     * Returns a resource representing the stream.
     *
     * @param StreamInterface $stream The stream to get a resource for
     *
     * @return resource
     * @throws \InvalidArgumentException if stream is not readable or writable
     */
    public static function getResource(StreamInterface $stream)
    {
        self::register();

        if ($stream->isReadable()) {
            $mode = $stream->isWritable() ? 'r+' : 'r';
        } elseif ($stream->isWritable()) {
            $mode = 'w';
        } else {
            throw new \InvalidArgumentException('The stream must be readable, '
                . 'writable, or both.');
        }

        return fopen('casper://stream', $mode, null, stream_context_create(array(
            'casper' => array('stream' => $stream)
        )));
    }

    /**
     * Registers the stream wrapper if needed
     */
    public static function register()
    {
        if (!in_array('casper', stream_get_wrappers())) {
            stream_wrapper_register('casper', __CLASS__);
        }
    }

    public function open(string $path, int $mode, array $options, string &$opened_path)
    {
        $options = stream_context_get_options($this->context);

        if (!isset($options['casper']['stream'])) {
            return false;
        }

        $this->mode = $mode;
        $this->stream = $options['casper']['stream'];

        return true;
    }

    public function read(int $count)
    {
        return $this->stream->read($count);
    }

    public function write(string $data)
    {
        return (int) $this->stream->write($data);
    }

    public function tell()
    {
        return $this->stream->tell();
    }

    public function eof(): bool
    {
        return $this->stream->eof();
    }

    public function seek(int $offset, $whence): bool
    {
        $this->stream->seek($offset, $whence);

        return true;
    }

    public function getMetadata(): array
    {
        static $modeMap = array(
            'r'  => 33060,
            'r+' => 33206,
            'w'  => 33188
        );

        return array(
            'dev'     => 0,
            'ino'     => 0,
            'mode'    => $modeMap[$this->mode],
            'nlink'   => 0,
            'uid'     => 0,
            'gid'     => 0,
            'rdev'    => 0,
            'size'    => $this->stream->getSize() ?: 0,
            'atime'   => 0,
            'mtime'   => 0,
            'ctime'   => 0,
            'blksize' => 0,
            'blocks'  => 0
        );
    }
}
