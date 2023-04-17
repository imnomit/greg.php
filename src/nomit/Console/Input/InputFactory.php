<?php

namespace nomit\Console\Input;

use nomit\Console\Input\InputInterface;
use nomit\Console\Input\Reader\ReaderInterface;

class InputFactory
{

    protected ReaderInterface $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function factory(InputInterface $input): InputInterface
    {
        $input->setReader($this->reader);

        return $input;
    }

}