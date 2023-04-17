<?php

namespace nomit\Console\Output\Writer;

use nomit\Stream\StreamInterface;

class StdErrWriter extends StreamWriter implements ErrorWriterInterface
{

    public function __construct()
    {
        if(defined('STDERR') && $this->hasStdErrSupport()) {
            $stream = STDERR;
        } else {
            $stream = @fopen('php://stderr', 'w') ?: fopen('php://output', 'w');
        }

        parent::__construct($stream);
    }

    public function getName(): string
    {
        return 'StdErrWriter';
    }

    public function write(string|\Throwable $content): void
    {
        if($content instanceof \Throwable) {
            $content = $content->getMessage();
        }

        parent::write($content);
    }

}