<?php

namespace nomit\Console\Output;

use nomit\Console\Format\Formatter;
use nomit\Console\Format\FormatterInterface;
use nomit\Console\Format\OutputFormatter;
use nomit\Console\Format\OutputFormatterInterface;
use nomit\Dumper\Dumper;
use nomit\Utility\Bag\BagInterface;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\Cursor\Cursor;
use nomit\Console\Output\Cursor\CursorInterface;
use nomit\Console\Output\Writer\ErrorWriterInterface;
use nomit\Console\Output\Writer\StdErrWriter;
use nomit\Console\Output\Writer\StdOutWriter;
use nomit\Console\Output\Writer\WriterInterface;
use nomit\Console\Output\Writer\BufferWriter;
use nomit\Console\Terminal\Terminal;
use nomit\Console\Terminal\TerminalInterface;
use nomit\Stream\StreamFactory;
use nomit\Utility\Bag\Bag;
use JetBrains\PhpStorm\Pure;

class Output implements OutputInterface
{

    protected InputInterface $input;

    protected TerminalInterface $terminal;

    protected CursorInterface $cursor;

    protected BagInterface $writers;

    protected array $buffer = [];

    protected bool $use_new_line = false;

    protected int $verbosity;

    protected bool $sent = false;

    protected OutputFormatterInterface $formatter;

    protected bool $write_to_error = false;

    public function __construct(
        InputInterface $input,
        iterable $writers = null,
        int $verbosity = self::VERBOSITY_NORMAL,
        bool $decorated = false,
        OutputFormatterInterface $formatter = null
    )
    {
        if(!$writers) {
            $writers = $this->getDefaultWriters();
        }

        $this->setWriters($writers);

        $this->input = $input;
        $this->verbosity = $verbosity;
        $this->terminal = new Terminal();
        $this->cursor = new Cursor($this, $input);
        $this->formatter = $formatter ?? new OutputFormatter();

        $this->formatter->setDecorated($decorated);
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getTerminal(): TerminalInterface
    {
        return $this->terminal;
    }

    public function getCursor(): CursorInterface
    {
        return $this->cursor;
    }

    public function setWriters(iterable $writers): self
    {
        $this->writers = new Bag();

        foreach($writers as $writer) {
            $this->addWriter($writer);
        }

        return $this;
    }

    public function getWriters(): array
    {
        return $this->writers->all();
    }

    public function addWriter(WriterInterface $writer): self
    {
        $this->writers->push($writer);

        return $this;
    }

    public function hasWriter(string $name): bool
    {
        return $this->writers->has($name);
    }

    public function getWriter(string $name): ?WriterInterface
    {
        return $this->writers->get($name);
    }

    public function getErrorWriter(): ErrorWriterInterface
    {
        foreach($this->writers as $writer) {
            if($writer instanceof ErrorWriterInterface) {
                return $writer;
            }
        }

        return $this->writers[] = new StdErrWriter();
    }

    public function error(string|iterable $messages = null, bool $newline = false, string $writerName = null,
                          int $options = self::OUTPUT_NORMAL): OutputInterface
    {
        $this->write_to_error = !$this->write_to_error;

        if($messages === null) {
            return $this;
        }

        return $this->write($messages, $newline, $writerName, $options);
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->formatter;
    }

    public function isDecorated(): bool
    {
        return $this->formatter->isDecorated();
    }

    public function useSameLine(): self
    {
        $this->use_new_line = false;

        return $this;
    }

    public function line(): self
    {
        $this->use_new_line = true;

        return $this;
    }

    public function write(string|iterable $messages, bool $newline = false, string $writerName = null,
                          int $options = self::OUTPUT_NORMAL): self
    {
        if(!is_iterable($messages)) {
            $messages = [$messages];
        }

        $types = self::OUTPUT_NORMAL | self::OUTPUT_RAW | self::OUTPUT_PLAIN;
        $type = $types & $options ?: self::OUTPUT_NORMAL;

        $verbosities = self::VERBOSITY_QUIET | self::VERBOSITY_NORMAL | self::VERBOSITY_VERBOSE | self::VERBOSITY_VERY_VERBOSE | self::VERBOSITY_DEBUG;
        $verbosity = $verbosities & $options ?: self::VERBOSITY_NORMAL;

        if ($verbosity > $this->getVerbosity()) {
            return $this;
        }

        if($this->use_new_line || $newline) {
            foreach($messages as $index => $message) {
                $messages[$index] .= PHP_EOL;
            }

            $this->use_new_line = false;
        }

        foreach ($messages as $message) {
            if($type === self::OUTPUT_NORMAL) {
                $message = $this->formatter->format($message);
            } else if($type === self::OUTPUT_PLAIN) {
                $message = strip_tags($this->formatter->format($message));
            }

            if($message && $verbosity <= $this->getVerbosity()) {
                if($writerName && ($writer = $this->getWriter($writerName))) {
                    $writer->write($message);
                } else {
                    foreach ($this->writers->all() as $writer) {
                        $writer->write($message);
                    }
                }
            }
        }

        $this->sent = true;

        return $this;
    }

    public function buffer(string|array $element, string $writerName = null): self
    {
        if(is_array($element)) {
            foreach($element as $message) {
                $this->buffer($message, $writerName);
            }

            return $this;
        }

        if($writerName) {
            $this->buffer[$writerName] = $element;
        } else {
            $this->buffer[] = $element;
        }

        return $this;
    }

    public function flush(bool $toString = false): string|array
    {
        $result = $this->read($toString);

        foreach($this->buffer as $id => $content) {
            if($this->hasWriter($id)) {
                $this->getWriter($id)?->write($content);
            } else {
                $this->write($content);
            }
        }

        $this->empty();

        $this->sent = true;

        return $result;
    }

    public function empty(): self
    {
        $this->buffer = [];

        return $this;
    }

    public function read(int $length = null, bool $toString = false): string|array
    {
        return $toString ? implode(PHP_EOL, $this->buffer) : $this->buffer;
    }

    public function up(int $lines = 1): self
    {
        $this->cursor->up($lines);

        return $this;
    }

    public function down(int $lines = 1): self
    {
        $this->cursor->down($lines);

        return $this;
    }

    public function right(int $columns = 1): self
    {
        $this->cursor->right($columns);

        return $this;
    }

    public function left(int $columns = 1): self
    {
        $this->cursor->left($columns);

        return $this;
    }

    public function start(): self
    {
        $this->cursor->start();

        return $this;
    }

    public function delete(): self
    {
        $this->cursor->delete();

        return $this;
    }

    public function default(): self
    {
        $this->cursor->default();

        return $this;
    }

    public function next(int $times = 1): self
    {
        $this->write($this->cursor->next($times));

        return $this;
    }

    public function previous(int $times = 1): self
    {
        $this->write($this->cursor->previous($times));

        return $this;
    }

    public function erase(): self
    {
        $this->write($this->cursor->erase());

        return $this;
    }

    public function clear(): self
    {
        $this->write($this->cursor->clear());

        return $this;
    }

    public function clearUp(): self
    {
        $this->write($this->cursor->clearUp());

        return $this;
    }

    public function clearDown(): self
    {
        $this->write($this->cursor->clearDown());

        return $this;
    }

    public function move(int $x, int $y): self
    {
        $this->write($this->cursor->move($x, $y));

        return $this;
    }

    public function setVerbosity(int $level): self
    {
        $this->verbosity = $level;

        return $this;
    }

    public function getVerbosity(): int
    {
        return $this->verbosity;
    }

    public function isQuiet(): bool
    {
        return $this->getVerbosity() === self::VERBOSITY_QUIET;
    }

    public function isVerbose(): bool
    {
        return $this->getVerbosity() === self::VERBOSITY_VERBOSE;
    }

    public function isVeryVerbose(): bool
    {
        return $this->getVerbosity() === self::VERBOSITY_VERY_VERBOSE;
    }

    public function isDebug(): bool
    {
        return $this->getVerbosity() === self::VERBOSITY_DEBUG;
    }

    protected function getDefaultWriters(): array
    {
        return [
            new StdOutWriter(),
            new BufferWriter(),
            new StdErrWriter(),
        ];
    }
}