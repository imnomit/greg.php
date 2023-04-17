<?php

namespace nomit\Console\Event;

use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\EventDispatcher\Event;

class ConsoleEvent extends Event
{

    protected InputInterface $input;

    protected ?OutputInterface $output;

    public function __construct(InputInterface $input, OutputInterface $output = null)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @param OutputInterface $output
     * @return ConsoleEvent
     */
    public function setOutput(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return ?OutputInterface
     */
    public function getOutput(): ?OutputInterface
    {
        return $this->output;
    }

}