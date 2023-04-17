<?php

namespace nomit\Console\Utilities;

use nomit\Console\Input\InputInterface;

interface InputAwareInterface
{

    public function setInput(InputInterface $input): self;

}