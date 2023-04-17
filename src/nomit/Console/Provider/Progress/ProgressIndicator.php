<?php

namespace nomit\Console\Provider\Progress;

use nomit\Console\Exception\LogicException;
use nomit\Console\Format\Formatter;
use nomit\Console\Output\OutputInterface;

class ProgressIndicator implements ProgressIndicatorInterface
{

    protected static ?array $formatters = null;

    protected OutputInterface $output;

    protected int $start_time;

    protected ?string $format;

    protected ?string $message = null;

    protected array $indicator_values = [];

    protected $indicator_current;

    protected int $indicator_change_interval;

    protected int $indicator_update_time;

    protected bool $started = false;

    public static function getFormatDefinition(string $name): ?string
    {
        return self::FORMATS[$name] ?? null;
    }

    public static function setPlaceholderFormatterDefinition(string $name, callable $callback): void
    {
        if(!self::$formatters) {
            self::$formatters = self::initializePlaceholderFormatters();
        }

        self::$formatters[$name] = $callback;
    }

    public static function getPlaceholderFormatterDefinition(string $name): ?callable
    {
        if(!self::$formatters) {
            self::$formatters = self::initializePlaceholderFormatters();
        }

        return self::$formatters[$name] ?? null;
    }

    protected static function initializePlaceholderFormatters(): array
    {
        return [
            'indicator' => function (self $indicator) {
                return $indicator->indicator_values[$indicator->indicator_current % \count($indicator->indicator_values)];
            },
            'message' => function (self $indicator) {
                return $indicator->message;
            },
            'elapsed' => function (self $indicator) {
                return Formatter::time(time() - $indicator->start_time);
            },
            'memory' => function () {
                return Formatter::memory(memory_get_usage(true));
            },
        ];
    }

    public function setMessage(?string $message): ProgressIndicatorInterface
    {
        $this->message = $message;

        $this->display();

        return $this;
    }

    public function start(string $message): void
    {
        if ($this->started) {
            throw new LogicException('The current progress indicator object has already been started.');
        }

        $this->message = $message;
        $this->started = true;
        $this->start_time = time();
        $this->indicator_update_time = $this->getCurrentTimeInMilliseconds() + $this->indicator_change_interval;
        $this->indicator_current = 0;

        $this->display();
    }

    public function advance(): void
    {
        if (!$this->started) {
            throw new LogicException('The current progress indicator has not yet been started.');
        }

        if (!$this->output->isDecorated()) {
            return;
        }

        $currentTime = $this->getCurrentTimeInMilliseconds();

        if ($currentTime < $this->indicator_update_time) {
            return;
        }

        $this->indicator_update_time = $currentTime + $this->indicator_change_interval;

        $this->indicator_current++;

        $this->display();
    }

    public function finish(string $message): void
    {
        if(!$this->start_time) {
            throw new LogicException('The current progress indicator object has not yet been started.');
        }

        $this->message = $message;

        $this->display();

        $this->output->line()->write('');

        $this->started = false;
    }

    protected function display(): void
    {
        if (OutputInterface::VERBOSITY_QUIET === $this->output->getVerbosity()) {
            return;
        }

        $this->overwrite(preg_replace_callback("{%([a-z\-_]+)(?:\:([^%]+))?%}i", function ($matches) {
            if ($formatter = self::getPlaceholderFormatterDefinition($matches[1])) {
                return $formatter($this);
            }

            return $matches[0];
        }, $this->format ?? ''));
    }

    protected function determineBestFormat(): string
    {
        switch ($this->output->getVerbosity()) {
            // OutputInterface::VERBOSITY_QUIET: display is disabled anyway
            case OutputInterface::VERBOSITY_VERBOSE:
                return $this->output->isDecorated() ? 'verbose' : 'verbose_no_ansi';
            case OutputInterface::VERBOSITY_VERY_VERBOSE:
            case OutputInterface::VERBOSITY_DEBUG:
                return $this->output->isDecorated() ? 'very_verbose' : 'very_verbose_no_ansi';
            default:
                return $this->output->isDecorated() ? 'normal' : 'normal_no_ansi';
        }
    }

    protected function overwrite(string $message): void
    {
        if ($this->output->isDecorated()) {
            $this->output->write("\x0D\x1B[2K");
            $this->output->write($message);
        } else {
            $this->output->line()->write($message);
        }
    }

    protected function getCurrentTimeInMilliseconds(): float
    {
        return round(microtime(true) * 1000);
    }

}