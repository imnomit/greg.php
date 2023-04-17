<?php

namespace nomit\Console\Exception;

use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;

class ConsoleException extends Exception implements ConsoleExceptionInterface
{

    protected $message;

    protected InputInterface $input;

    protected ?OutputInterface $output = null;

    protected ?\Throwable $exception = null;

    public function __construct(InputInterface $input, \Throwable $exception = null)
    {
        $this->input = $input;
        $this->exception = $exception;

        if($this->message) {
            $message = $this->message . ($exception !== null
                ? sprintf(': "%s".', $exception->getMessage())
                : '.');
        } else {
            $message = 'An error occurred while attempting to run the console' .
                ($exception !== null
                    ? sprintf(': "%s".', $exception->getMessage())
                    : '.');
        }

        parent::__construct(
            $message,
            $exception !== null ? $exception->getCode() : null,
            $exception
        );
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @param OutputInterface|null $output
     */
    public function setOutput(?OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function hasOutput(): bool
    {
        return $this->output instanceof OutputInterface;
    }

    /**
     * @return OutputInterface|null
     */
    public function getOutput(): ?OutputInterface
    {
        return $this->output;
    }

    /**
     * @return \Throwable|null
     */
    public function getException(): ?\Throwable
    {
        return $this->exception;
    }

}