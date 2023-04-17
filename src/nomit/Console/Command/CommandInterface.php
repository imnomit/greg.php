<?php

namespace nomit\Console\Command;

use nomit\Stream\StreamInterface;
use Psr\Log\LoggerInterface;
use nomit\Console\Completion\CompletionInputInterface;
use nomit\Console\Completion\CompletionSuggestionsInterface;
use nomit\Console\ConsoleInterface;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Definition\DefinitionInterface;
use nomit\Console\Definition\Option\OptionInterface;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Console\Provider\ProviderCollectionInterface;
use nomit\Console\SynopsisInterface;
use nomit\Utility\Bag\BagInterface;
use nomit\Process\Action\ActionInterface;
use nomit\Process\ProcessInterface;

interface CommandInterface extends SynopsisInterface, LoggerInterface
{

    public const STATUS_NOT_READY = 0;
    public const STATUS_READY = 2;
    public const STATUS_STARTED = 4;
    public const STATUS_RUNNING = 8;
    public const STATUS_PAUSED = 16;
    public const STATUS_WAITING = 32;
    public const STATUS_TERMINATED = 64;
    public const STATUS_FAILURE = 128;
    public const STATUS_SUCCESS = 256;

    public const RESULT_NONE = 0;
    public const RESULT_INVALID = 2;
    public const RESULT_FAILURE = 4;
    public const RESULT_THROWABLE = 8;
    public const RESULT_SUCCESS = 16;

    public static function getDefaultName(): string;

    public static function getDefaultDescription(): ?string;

    public function getId(): string;

    public function setConsole(ConsoleInterface $console): self;

    public function getConsole(): ConsoleInterface;

    public function setName(string $name): self;

    public function hasName(): bool;

    public function getName(): ?string;

    public function setDefinition(DefinitionInterface $definition): self;

    public function getDefinition(): DefinitionInterface;

    public function mergeDefinitions(bool $mergeArguments = true): void;

    public function setTitle(string $title): self;

    public function getTitle(): ?string;

    public function setHidden(bool $hidden): self;

    public function isHidden(): bool;

    public function getClassName(): string;

    public function setAliases(array $aliases): self;

    public function addAlias(string $alias): self;

    public function getAliases(): array;

    public function getStatus(): int;

    public function hasStatus(int $status): bool;

    public function isStatus(int $status): bool;

    public function isNotReady(): bool;

    public function isReady(): bool;

    public function isStarted(): bool;

    public function isRunning(): bool;

    public function isTerminated(): bool;

    public function isFailure(): bool;

    public function isSuccessful(): bool;

    public function isRan(): bool;

    public function isEnabled(): bool;

    public function enable(): self;

    public function disable(): self;

    public function setHelp(string $help): self;

    public function getHelp(): ?string;

    public function setProcess(ProcessInterface $process): self;

    public function getProcess(): ?ProcessInterface;

    public function setAction(ActionInterface $action): self;

    public function getAction(): ?ActionInterface;

    public function setArguments(array|BagInterface $arguments): self;

    public function hasArgument(string $name): bool;

    public function addArgument(ArgumentInterface $argument): self;

    public function getArguments(): array;

    public function setOptions(array|BagInterface $options): self;

    public function addOption(OptionInterface $option): self;

    public function hasOption(string $name): bool;

    public function getOption(string $name): ?OptionInterface;

    public function getOptions(): array;

    public function setException(int $index, \Throwable $exception): self;

    public function unshiftException(\Throwable $exception): self;

    public function popException(): ?\Throwable;

    public function shiftException(): ?\Throwable;

    public function getExceptions(): array;

    public function catch(bool $catch = null): self;

    public function configure(): void;

    public function interact(InputInterface $input, OutputInterface $output): void;

    public function prepare(InputInterface $input, OutputInterface $output): void;

    public function run(InputInterface $input, OutputInterface $output): int;

    public function wait(): self;

    public function stop(): void;

    public function complete(CompletionInputInterface $input, CompletionSuggestionsInterface $suggestions): void;

    public function addUsage(string $usage): self;

    public function getUsages(): array;

    public function setProviders(ProviderCollectionInterface $providers): self;

    public function getProviders(): ProviderCollectionInterface;

    public function getProvider(string $name): mixed;

    public function isDue(\DateTimeInterface $dateTime = null): bool;

    public function output(string|array|StreamInterface $target): self;

    public function lock(string $temporaryDirectory = null): self;

    public function isLocked(): bool;

    public function unlock(): void;

    public function before(callable $event): self;

    public function after(callable $event): self;

    public function at(string $expression): self;

    public function date(string|\DateTimeInterface $date): self;

    public function everyMinute(string|int|null $minute = null): self;

    public function hourly(int|string $minute = 0): self;

    public function daily(int|string $hour = 0, int|string $minute = 0): self;

    public function weekly(int|string $weekday = 0, int|string $hour = 0, int|string $minute = 0): self;

    public function monthly(int|string $month = '*', int|string $day = 1, int|string $hour = 0, int|string $minute = 0): self;

}