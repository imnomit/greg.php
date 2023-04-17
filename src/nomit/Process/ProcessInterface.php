<?php

namespace nomit\Process;

use nomit\Console\Command\CommandInterface;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;

interface ProcessInterface
{

    public function isRunning(): bool;

    public function kill(): bool;

    public function run(InputInterface $input, OutputInterface $output, CommandInterface $command): int;

    public function wait(): bool;

    public function terminate(): bool;

}