<?php

namespace nomit\Console\Component;

use nomit\Console\Format\Formatter;
use nomit\Console\Format\FormatterInterface;
use nomit\Console\Format\Style\StyleInterface;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;

class Component implements ComponentInterface
{

    protected InputInterface $input;

    protected OutputInterface $output;

    protected StyleInterface $style;

    protected FormatterInterface $formatter;

    protected bool $buffering = false;

    public function __construct(InputInterface $input, OutputInterface $output, StyleInterface $style)
    {
        $this->input = $input;
        $this->output = $output;
        $this->style = $style;
        $this->formatter = new Formatter();
    }

    public function read(mixed $default = null, int $length = null, callable $callback = null): string
    {
        return $this->input->read($default, $length, $callback);
    }

    public function write(string|array $content): self
    {
        if($this->isBuffering()) {
            $this->output->buffer($content);
        } else {
            $this->output->write($content);
        }

        return $this;
    }

    public function writeTo(string $writer, string $content = null): self
    {
        if($this->isBuffering()) {
            $this->buffer($content);
        } else {
            $this->output->write($content, false, $writer);
        }

        return $this;
    }

    public function buffer(string|array $content = null): self
    {
        if($content === null) {
            $this->buffering = true;
        } else {
            $this->output->buffer($content);
        }

        return $this;
    }

    public function isBuffering(): bool
    {
        return $this->buffering;
    }

    public function flush(bool $toString = false): string|array
    {
        return $this->output->flush($toString);
    }

    public function format(string $message, array $styles = []): string
    {
        $formatter = new Formatter();

        foreach($styles as $name => $style) {
            $formatter->setStyle($name, $style);
        }

        return $formatter->format($message);
    }

}