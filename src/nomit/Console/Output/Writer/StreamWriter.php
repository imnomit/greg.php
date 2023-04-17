<?php

namespace nomit\Console\Output\Writer;

use nomit\Dumper\Dumper;
use nomit\Stream\StreamFactory;
use nomit\Stream\StreamInterface;

class StreamWriter implements WriterInterface
{

    protected StreamInterface $stream;

    public function __construct($stream = null)
    {
        $streamFactory = new StreamFactory();

        if(is_string($stream)) {
            $stream = $streamFactory->createStream($stream);
        } else if(is_resource($stream)) {
            $stream = $streamFactory->createStreamFromResource($stream);
        } else {
           $stream = $streamFactory->createStream();
       }

        $this->stream = $stream;
    }

    public function getName(): string
    {
        return 'StreamWriter';
    }

    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    public function write(string $content): void
    {
        $this->stream->write($content);
    }

    public function supportsColor(): bool
    {
        if (isset($_SERVER['NO_COLOR']) || false !== getenv('NO_COLOR')) {
            return false;
        }

        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }

        $stream = $this->stream->getResource();

        if (\DIRECTORY_SEPARATOR === '\\') {
            return (\function_exists('sapi_windows_vt100_support')
                    && @sapi_windows_vt100_support($stream))
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }

        return stream_isatty($stream);
    }

    protected function hasStdOutSupport(): bool
    {
        return false === $this->isRunningOs400();
    }

    protected function hasStdErrSupport(): bool
    {
        return false === $this->isRunningOs400();
    }

    protected function isRunningOs400(): bool
    {
        $checks = [
            \function_exists('php_uname') ? php_uname('s') : '',
            getenv('OSTYPE'),
            \PHP_OS,
        ];

        return false !== stripos(implode(';', $checks), 'OS400');
    }

}