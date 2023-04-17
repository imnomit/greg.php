<?php

namespace nomit\Console\Event;

use nomit\Console\Command\CommandInterface;
use nomit\Console\ConsoleInterface;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;

final class RunConsoleEvent extends ConsoleEvent
{

    private ?ResponseInterface $response = null;

    public function __construct(
        private ConsoleInterface $console,
        InputInterface $input,
        OutputInterface $output,
        private CommandInterface $command
    )
    {
        parent::__construct($input, $output);
    }

    /**
     * @return ConsoleInterface
     */
    public function getConsole(): ConsoleInterface
    {
        return $this->console;
    }

    /**
     * @return CommandInterface
     */
    public function getCommand(): CommandInterface
    {
        return $this->command;
    }

    public function getRequest(): RequestInterface
    {
        return $this->input->getRequest();
    }

    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

}