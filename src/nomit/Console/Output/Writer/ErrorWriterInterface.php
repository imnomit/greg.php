<?php

namespace nomit\Console\Output\Writer;

interface ErrorWriterInterface extends WriterInterface
{

    public function write(string|\Throwable $content): void;

}