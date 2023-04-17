<?php

namespace nomit\Console;

use nomit\Console\Command\Command;
use nomit\Console\Command\CommandInterface;
use nomit\Console\Command\CommandRepositoryInterface;
use nomit\Console\Command\CompleteCommand;
use nomit\Console\Command\DumpCompletionCommand;
use nomit\Console\Command\HelpCommand;
use nomit\Console\Command\LazyCommand;
use nomit\Console\Command\ListCommand;
use nomit\Console\Command\SignallingCommand;
use nomit\Console\Completion\CompletionInputInterface;
use nomit\Console\Completion\CompletionSuggestionsInterface;
use nomit\Console\Definition\Argument\Argument;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Definition\Definition;
use nomit\Console\Definition\DefinitionInterface;
use nomit\Console\Definition\Option\Option;
use nomit\Console\Event\CommandEvent;
use nomit\Console\Event\CommandEvents;
use nomit\Console\Event\ConsoleEvents;
use nomit\Console\Event\ExceptionConsoleEvent;
use nomit\Console\Event\ExecutedCommandEvent;
use nomit\Console\Event\FailedCommandEvent;
use nomit\Console\Event\ExecuteCommandEvent;
use nomit\Console\Event\PerformCommandEvent;
use nomit\Console\Event\ResolvedCommandEvent;
use nomit\Console\Event\RunConsoleEvent;
use nomit\Console\Event\SignalCommandEvent;
use nomit\Console\Event\SuccessfulCommandEvent;
use nomit\Console\Event\TerminateConsoleEvent;
use nomit\Console\Exception\LogicException;
use nomit\Console\Exception\RunCommandConsoleException;
use nomit\Console\Exception\RuntimeException;
use nomit\Console\Exception\UnresolvableCommandConsoleException;
use nomit\Console\Exception\UnresolvableNamespaceConsoleException;
use nomit\Console\Format\Formatter;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\ArrayInput;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Console\Provider\FormatterProvider;
use nomit\Console\Provider\ProviderCollection;
use nomit\Console\Provider\ProviderCollectionInterface;
use nomit\Console\Provider\ProviderInterface;
use nomit\Console\Provider\QuestionProvider;
use nomit\Console\Queue\QueueInterface;
use nomit\Console\Shell\ShellFactory;
use nomit\Console\Shell\ShellInterface;
use nomit\Console\Signal\SignalRegistry;
use nomit\Console\Signal\SignalRegistryInterface;
use nomit\Console\Terminal\Terminal;
use nomit\Console\Terminal\TerminalInterface;
use nomit\Console\Utilities\InputAwareInterface;
use nomit\Console\Utilities\Sleeper;
use nomit\Console\Utilities\SleeperInterface;
use nomit\DependencyInjection\ContainerAwareInterface;
use nomit\Dumper\Dumper;
use nomit\Error\ErrorHandler;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Exception\ExceptionInterface;
use Psr\Container\ContainerInterface;

class Console implements ConsoleInterface
{

    private string $name;

    private bool $debug = false;

    private string $version;

    private string $default_command;

    private ShellInterface $shell;

    private TerminalInterface $terminal;

    private SleeperInterface $sleeper;

    protected CommandRepositoryInterface $repository;

    protected ?EventDispatcherInterface $dispatcher;

    protected ?ContainerInterface $container = null;

    private bool $throwOnUnresolvedCommand = false;

    private bool $ignoreValidationErrors = false;

    private bool $catch = false;

    private bool $isSingleCommand = false;

    private array $commands = [];

    private bool $booted = false;

    private bool $exit = false;

    private bool $wantsHelp = false;

    private ?CommandInterface $runningCommand = null;

    private ?CommandInterface $lastCommand = null;

    private ?DefinitionInterface $definition = null;

    protected ?ProviderCollectionInterface $providers = null;

    private ?SignalRegistryInterface $signalRegistry = null;

    private array $signalsToDispatchEvent = [];

    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        $this->name = $name;
        $this->version = $version;
        $this->default_command = Command::$default_name;

        $shellFactory = new ShellFactory();

        $this->shell = $shellFactory->factory();
        $this->terminal = new Terminal($this->shell);
        $this->sleeper = new Sleeper();

        if(defined('SIGIN') && SignalRegistry::isSupported()) {
            $this->signalRegistry = new SignalRegistry();

            $this->signalsToDispatchEvent = [\SIGINT, \SIGTERM, \SIGUSR1, \SIGUSR2];
        }
    }

    public function setCommandRepository(CommandRepositoryInterface $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function getCommandRepository(): CommandRepositoryInterface
    {
        return $this->repository;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDebug(bool $debug = true): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setContainer(ContainerInterface $container): \nomit\DependencyInjection\ContainerAwareInterface
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLongVersion(): string
    {
        if ('UNKNOWN' !== $this->getName()) {
            if ('UNKNOWN' !== $this->getVersion()) {
                return sprintf('%s <info>%s</>', $this->getName(), $this->getVersion());
            }

            return $this->getName();
        }

        return 'Console Tool';
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function getSignalRegistry(): SignalRegistryInterface
    {
        if(!$this->signalRegistry) {
            throw new RuntimeException('Console command signalling is not supported by the current environment.');
        }

        return $this->signalRegistry;
    }

    public function setSignalsToDispatchEvent(int ...$signalsToDispatchEvent): self
    {
        $this->signalsToDispatchEvent = $signalsToDispatchEvent;

        return $this;
    }

    protected function boot(): void
    {
        if($this->booted) {
            return;
        }

        $this->booted = true;

        foreach($this->getDefaultCommands() as $command) {
            $this->add($command);
        }
    }

    public function setDefaultCommand(string $commandName, bool $isSingleCommand = false): self
    {
        $this->default_command = explode('|', ltrim($commandName, '|'))[0];

        if ($isSingleCommand) {
            // Ensure the command exist
            $this->get($commandName);

            $this->isSingleCommand = true;
        }

        return $this;
    }

    public function isSingleCommand(): bool
    {
        return $this->isSingleCommand;
    }

    protected function getCommandName(InputInterface $input): ?string
    {
        return $this->isSingleCommand ? $this->default_command : $input->getFirstArgument();
    }

    public function getDefaultCommand(): string
    {
        return $this->default_command;
    }

    public function setDefinition(DefinitionInterface $definition): self
    {
        $this->definition = $definition;

        return $this;
    }

    public function getDefinition(): DefinitionInterface
    {
        if (!$this->definition) {
            $this->definition = $this->getDefaultDefinition();
        }

        $this->definition->setDefaultCommand($this->getDefaultCommand());

        if ($this->isSingleCommand()) {
            $inputDefinition = $this->definition;
            $inputDefinition->setArguments([]);

            return $inputDefinition;
        }

        return $this->definition;
    }

    public function ignoreValidationErrors(): self
    {
        $this->ignoreValidationErrors = true;

        return $this;
    }

    public function add(CommandInterface $command): ?CommandInterface
    {
        $this->boot();

        $command->setConsole($this);

        if(!$command->isEnabled()) {
            $command->setConsole(null);

            return null;
        }

        if(!$command instanceof LazyCommand) {
            $command->getDefinition();
        }

        if(!$command->getName()) {
            throw new LogicException(sprintf('The command defined within "%s" cannot have an empty name.', get_debug_type($command)));
        }

        $this->commands[$command->getName()] = $command;

        foreach($command->getAliases() as $alias) {
            $this->commands[$alias] = $command;
        }

        return $command;
    }

    public function has(string $name): bool
    {
        $this->boot();

        return isset($this->commands[$name]) || ($this->repository->has($name) && $this->add($this->repository->get($name)));
    }

    public function get(string $name): CommandInterface
    {
        $this->boot();

        if (!$this->has($name)) {
            throw new UnresolvableCommandConsoleException(sprintf('The command "%s" does not exist.', $name));
        }

        if (!isset($this->commands[$name])) {
            throw new UnresolvableCommandConsoleException(sprintf('The "%s" command cannot be found because it is registered under multiple names. Make sure you don\'t set a different name via constructor or "setName()".', $name));
        }

        $command = $this->commands[$name];

        if ($this->wantsHelp) {
            $this->wantsHelp = false;

            $helpCommand = $this->get('help');
            $helpCommand->setCommand($command);

            return $helpCommand;
        }

        return $command;
    }

    public function all(string $namespace = null): array
    {
        $this->boot();

        if (null === $namespace) {
            if (!$this->repository) {
                return $this->commands;
            }

            $commands = $this->commands;

            foreach ($this->repository->getNames() as $index => $name) {
                if (!isset($commands[$name]) && $this->has($name)) {
                    $commands[$name] = $this->get($name);
                }
            }

            return $commands;
        }

        $commands = [];

        foreach ($this->commands as $name => $command) {
            if ($namespace === $this->extractNamespace($name, substr_count($namespace, ':') + 1)) {
                $commands[$name] = $command;
            }
        }

        if ($this->repository) {
            foreach ($this->repository->getNames() as $name) {
                if (!isset($commands[$name]) && $namespace === $this->extractNamespace($name, substr_count($namespace, ':') + 1) && $this->has($name)) {
                    $commands[$name] = $this->get($name);
                }
            }
        }

        return $commands;
    }

    public function extractNamespace(string $name, int $limit = null): string
    {
        $parts = explode(':', $name, -1);

        return implode(':', null === $limit ? $parts : \array_slice($parts, 0, $limit));
    }

    public function getNamespaces(): array
    {
        $namespaces = [];

        /**
         * @var CommandInterface $command
         */
        foreach ($this->all() as $command) {
            if ($command->isHidden() || !$command->hasName()) {
                continue;
            }

            $namespaces[] = $this->extractAllNamespaces($command->getName());

            foreach ($command->getAliases() as $alias) {
                $namespaces[] = $this->extractAllNamespaces($alias);
            }
        }

        return array_values(array_unique(array_filter(array_merge([], ...$namespaces))));
    }

    /**
     * Finds a registered namespace by a name or an abbreviation.
     *
     * @throws UnresolvableNamespaceConsoleException When namespace is incorrect or ambiguous
     */
    public function resolveNamespace(string $namespace): string
    {
        $allNamespaces = $this->getNamespaces();
        $expr = implode('[^:]*:', array_map('preg_quote', explode(':', $namespace))).'[^:]*';
        $namespaces = preg_grep('{^'.$expr.'}', $allNamespaces);

        if (empty($namespaces)) {
            $message = sprintf('There are no commands defined in the "%s" namespace.', $namespace);

            if ($alternatives = $this->getAlternatives($namespace, $allNamespaces)) {
                if (1 == \count($alternatives)) {
                    $message .= "\n\nDid you mean this?\n    ";
                } else {
                    $message .= "\n\nDid you mean one of these?\n    ";
                }

                $message .= implode("\n    ", $alternatives);
            }

            throw new UnresolvableNamespaceConsoleException($message, $alternatives);
        }

        $exact = \in_array($namespace, $namespaces, true);

        if (\count($namespaces) > 1 && !$exact) {
            throw new UnresolvableNamespaceConsoleException(sprintf("The namespace \"%s\" is ambiguous.\nDid you mean one of these?\n%s.", $namespace, $this->getAbbreviationSuggestions(array_values($namespaces))), array_values($namespaces));
        }

        return $exact ? $namespace : reset($namespaces);
    }

    /**
     * Returns all namespaces of the command name.
     *
     * @return string[]
     */
    protected function extractAllNamespaces(string $name): array
    {
        // -1 as third argument is needed to skip the command short name when exploding
        $parts = explode(':', $name, -1);
        $namespaces = [];

        foreach ($parts as $part) {
            if (\count($namespaces)) {
                $namespaces[] = end($namespaces).':'.$part;
            } else {
                $namespaces[] = $part;
            }
        }

        return $namespaces;
    }

    public function resolve(string $name): CommandInterface
    {
        $this->boot();

        $aliases = [];

        foreach($this->commands as $command) {
            foreach($command->getAliases() as $alias) {
                if(!$this->has($alias)) {
                    $this->commands[$alias] = $command;
                }
            }
        }

        if($this->has($name)) {
            return $this->get($name);
        }

        $allCommands = $this->repository->getNames();
        $expr = implode('[^:]*:', array_map('preg_quote', explode(':', $name))) . '[^:]*';
        $commands = preg_grep('{^' . $expr . '}', $allCommands);

        if (empty($commands)) {
            $commands = preg_grep('{^' . $expr . '}i', $allCommands);
        }

        // if no commands matched or we just matched namespaces
        if (empty($commands) || \count(preg_grep('{^' . $expr . '$}i', $commands)) < 1) {
            $message = sprintf('Command "%s" is not defined.', $name);

            if ($alternatives = $this->getAlternatives($name, $allCommands)) {
                // remove hidden commands
                $alternatives = array_filter($alternatives, function ($name) {
                    return !$this->repository->get($name)->isHidden();
                });

                if (1 === \count($alternatives)) {
                    $message .= "\n\nDid you mean this?\n    ";
                } else {
                    $message .= "\n\nDid you mean one of these?\n    ";
                }
                $message .= implode("\n    ", $alternatives);
            }

            throw new UnresolvableCommandConsoleException($message, array_values($alternatives));
        }

        // filter out aliases for commands which are already on the list
        if (\count($commands) > 1) {
            $commandList = array_merge(array_flip($this->repository->getNames()), $this->commands);
            $commands = array_unique(array_filter($commands, function ($nameOrAlias) use (&$commandList, $commands, &$aliases) {
                if (!$commandList[$nameOrAlias] instanceof CommandInterface) {
                    $commandList[$nameOrAlias] = $this->repository->get($nameOrAlias);
                }

                $commandName = $commandList[$nameOrAlias]->getName();

                $aliases[$nameOrAlias] = $commandName;

                return $commandName === $nameOrAlias || !\in_array($commandName, $commands);
            }));
        }

        if (\count($commands) > 1) {
            $usableWidth = $this->terminal->getWidth() - 10;
            $abbreviations = array_values($commands);
            $maxLen = 0;

            foreach ($abbreviations as $abbreviation) {
                $maxLen = max(Formatter::width($abbreviation), $maxLen);
            }

            $abbreviations = array_map(function ($cmd) use ($commandList, $usableWidth, $maxLen, &$commands) {
                if ($commandList[$cmd]->isHidden()) {
                    unset($commands[array_search($cmd, $commands)]);

                    return false;
                }

                $abbrev = str_pad($cmd, $maxLen, ' ') . ' ' . $commandList[$cmd]->getDescription();

                return Formatter::width($abbrev) > $usableWidth ? Formatter::substr($abbrev, 0, $usableWidth - 3) . '...' : $abbrev;
            }, array_values($commands));

            if (\count($commands) > 1) {
                $suggestions = $this->getAbbreviationSuggestions(array_filter($abbreviations));

                throw new UnresolvableCommandConsoleException(sprintf("Command \"%s\" is ambiguous.\nDid you mean one of these?\n%s.", $name, $suggestions), array_values($commands));
            }
        }

        $command = $this->get(reset($commands));

        if ($command->isHidden()) {
            throw new UnresolvableCommandConsoleException(sprintf('The command "%s" does not exist.', $name));
        }

        $this->dispatcher?->dispatch(new ResolvedCommandEvent($command), CommandEvents::RESOLVED);

        return $command;
    }

    protected function getAbbreviationSuggestions(array $abbrevs): string
    {
        return '    ' . implode("\n    ", $abbrevs);
    }

    protected function getAlternatives(string $name, iterable $collection): array
    {
        $threshold = 1e3;
        $alternatives = [];

        $collectionParts = [];

        foreach ($collection as $item) {
            $collectionParts[$item] = explode(':', $item);
        }

        foreach (explode(':', $name) as $i => $subname) {
            foreach ($collectionParts as $collectionName => $parts) {
                $exists = isset($alternatives[$collectionName]);

                if (!isset($parts[$i]) && $exists) {
                    $alternatives[$collectionName] += $threshold;
                    continue;
                }

                if (!isset($parts[$i])) {
                    continue;
                }

                $lev = levenshtein($subname, $parts[$i]);

                if ($lev <= \strlen($subname) / 3 || '' !== $subname && str_contains($parts[$i], $subname)) {
                    $alternatives[$collectionName] = $exists ? $alternatives[$collectionName] + $lev : $lev;
                } elseif ($exists) {
                    $alternatives[$collectionName] += $threshold;
                }
            }
        }

        foreach ($collection as $item) {
            $lev = levenshtein($name, $item);

            if ($lev <= \strlen($name) / 3 || str_contains($item, $name)) {
                $alternatives[$item] = isset($alternatives[$item]) ? $alternatives[$item] - $lev : $lev;
            }
        }

        $alternatives = array_filter($alternatives, function ($lev) use ($threshold) { return $lev < 2 * $threshold; });

        ksort($alternatives, \SORT_NATURAL | \SORT_FLAG_CASE);

        return array_keys($alternatives);
    }

    public function dispatch(InputInterface $input, OutputInterface $output): int
    {
        if(true === $input->hasParameterOption(['--version', '-V'], true)) {
            $output->line()->write($this->getLongVersion());
        }

        try {
            $input->bind($this->getDefinition());
        } catch(\nomit\Console\Exception\ExceptionInterface $exception) {}

        $name = $this->getCommandName($input);

        if(true === $input->hasParameterOption(['--help', '-h'], true)) {
            if(!$name) {
                $name = 'help';
                $input = new ArrayInput([
                    'command_name' => $this->getDefaultCommand()
                ]);
            } else {
                $this->wantsHelp = true;
            }
        }

        if(!$name) {
            $name = $this->default_command;
            $definition = $this->getDefinition();

            $definition->setArguments(array_merge(
                $definition->getArguments(),
                [
                    'command' => new Argument('command', Argument::OPTIONAL, $definition->getArgument('command')->getDescription(), $name)
                ]
            ));
        }

        try {
            $this->runningCommand = null;

            $command = $this->resolve($name);
        } catch(\Throwable $exception) {
            if (($this->throwOnUnresolvedCommand && $exception instanceof UnresolvableCommandConsoleException)
                || !($exception instanceof UnresolvableCommandConsoleException && !$exception instanceof UnresolvableNamespaceConsoleException)
                || 1 !== \count($alternatives = $exception->getAlternatives())
                || !$input->isInteractive()
            ) {
                $this->throw($input, $output, $exception, $this->catch);
            }

            $command = null;

            if(isset($alternatives) && count($alternatives) > 0) {
                $alternative = $alternatives[0];

                $style = new ConsoleStyle($input, $output);
                $style->block(sprintf("\nCommand \"%s\" is not defined.\n", $name), null, 'error');

                if (!$style->confirm(sprintf('Do you want to run "%s" instead? ', $alternative), false)) {
                    if (null !== $this->dispatcher) {
                        $event = $this->except($input, $output, $exception);

                        return $event->getExitCode();
                    }

                    return 1;
                }

                $command = $this->resolve($alternative);
            }
        }

        if(!$command instanceof CommandInterface) {
            $command = $this->resolve($this->getDefaultCommand());
            $definition = $this->getDefinition();

            $definition->setArguments(array_merge(
                $definition->getArguments(),
                [
                    'command' => new Argument('command', ArgumentInterface::OPTIONAL, $definition->getArgument('command')->getDescription(), $name)
                ]
            ));
        }

        $input->setCommand($command->getName());

        if($command instanceof LazyCommand) {
            $command = $command->getCommand();
        }

        return $this->run($input, $output, $command);
    }

    public function run(InputInterface $input, OutputInterface $output, CommandInterface $command): int
    {
        if($command->getConsole() === null) {
            $command->setConsole($this);
        }

        $this->runningCommand = $command;

        $exitCode = CommandInterface::RESULT_NONE;

        try {
            $this->dispatcher?->dispatch(new RunConsoleEvent($this, $input, $output, $command), ConsoleEvents::RUN);

            $exitCode = $this->execute($input, $output, $command);
        } catch(\Throwable $exception) {
            if(!$this->catch) {
                throw $exception;
            }

            $event = $this->except($input, $output, $exception);

            $exitCode = $event->getExitCode();
            $exception = $event->getThrowable();

            while($previousException = $exception->getPrevious()) {
                $this->renderException($output, $previousException);
            }
        } finally {
            $this->runningCommand = null;

            return $this->terminate($exitCode, $input, $output, $command);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output, CommandInterface $command): int
    {
        if (\function_exists('putenv')) {
            @putenv('LINES='.$this->terminal->getHeight());
            @putenv('COLUMNS='.$this->terminal->getWidth());
        }

        $renderException = function (\Throwable $exception) use ($output) {
            $this->renderException($output, $exception);
        };

        if ($phpHandler = set_exception_handler($renderException)) {
            restore_exception_handler();

            if (!\is_array($phpHandler) || !$phpHandler[0] instanceof ErrorHandler) {
                $errorHandler = true;
            }
        }

        $this->configure($input, $output);

        try {
            [$exitCode, $exception] = $this->perform($input, $output, $command);
        } catch(\Throwable $exception) {
            $renderException($exception);

            $exitCode = $exception->getCode();

            if (is_numeric($exitCode)) {
                $exitCode = (int) $exitCode;

                if ($exitCode <= 0) {
                    $exitCode = 1;
                }
            } else {
                $exitCode = 1;
            }

            $this->throw($input, $output, $exception, $this->catch);
        } finally {
            $this->dispatcher?->dispatch(new ExecuteCommandEvent($input, $output, $command), CommandEvents::EXECUTE);

            if (!$phpHandler) {
                if (set_exception_handler($renderException) === $renderException) {
                    restore_exception_handler();
                }

                restore_exception_handler();
            } else if(!$errorHandler) {
                $finalHandler = $phpHandler[0]->setExceptionHandler(null);

                if($finalHandler !== $renderException) {
                    $phpHandler[0]->setExceptionHandler($finalHandler);
                }
            }

            $executedEvent = new ExecutedCommandEvent($exitCode, $input, $output, $command);

            $this->dispatcher?->dispatch($executedEvent, CommandEvents::EXECUTED);
        }

        if(CommandInterface::RESULT_SUCCESS === $exitCode & CommandInterface::RESULT_SUCCESS) {
            $this->succeed($input, $output, $command);
        } else {
            $this->fail($input, $output, $command, $exitCode);
        }

        if(null !== $exception) {
            $this->throw($input, $output, $exception, true);
        }

        if($this->exit) {
            if($exitCode > 255) {
                $exitCode = 255;
            }

            exit($exitCode);
        }

        return $exitCode;
    }

    protected function perform(InputInterface $input, OutputInterface $output, CommandInterface $command): array
    {
        $exception = null;

        foreach($command->getProviders() as $provider) {
            if($provider instanceof InputAwareInterface) {
                $provider->setInput($input);
            }
        }

        if($command instanceof SignallingCommand && ($this->signalsToDispatchEvent || $command->getSubscribedSignals())) {
            if(!$this->signalRegistry) {
                throw new RuntimeException('No signal events could be subscribed to because the current environment does not support "pcntl" signalling.');
            }

            if($this->terminal->hasSttyAvailable()) {
                $sttyMode = shell_exec('stty -g');

                foreach ([\SIGINT, \SIGTERM] as $signal) {
                    $this->signalRegistry->register($signal, static function () use ($sttyMode) {
                        shell_exec('stty '.$sttyMode);
                    });
                }
            }

            if($this->dispatcher !== null) {
                foreach ($this->signalsToDispatchEvent as $signal) {
                    $event = new SignalCommandEvent($input, $output, $command, $signal);

                    $this->signalRegistry->register($signal, function ($signal, $hasNext) use ($event) {
                        $this->dispatcher->dispatch($event, ConsoleEvents::SIGNAL);

                        // No more handlers, we try to simulate PHP default behavior
                        if (!$hasNext) {
                            if (!\in_array($signal, [\SIGUSR1, \SIGUSR2], true)) {
                                exit(0);
                            }
                        }
                    });
                }
            }

            foreach($command->getSubscribedSignals() as $signal) {
                $this->signalRegistry->register($signal, [$command, 'handleSignal']);
            }
        }

        try {
            $command->mergeDefinitions();

            $input->bind($command->getDefinition());
        } catch(\nomit\Console\Exception\ExceptionInterface $exception) {}

        $runEvent = new PerformCommandEvent($input, $output, $command);

        $this->dispatcher?->dispatch($runEvent, CommandEvents::PERFORM);

        try {
            if($runEvent->shouldPerform()) {
                $exitCode = $command->run($input, $output);
            } else {
                $exitCode = $runEvent::RETURN_CODE_DISABLED;
            }
        } catch(\nomit\Console\Exception\ExceptionInterface $exception) {
            $event = $this->except($input, $output, $exception, $command);
            $exception = $event->getThrowable();

            if(0 === ($exitCode = $event->getExitCode())) {
                $exception = null;
            }

            if(!$this->catch) {
                throw $exception;
            }
        }

        return [$exitCode, $exception];
    }

    protected function succeed(InputInterface $input, OutputInterface $output, CommandInterface $command): void
    {
        $this->dispatcher?->dispatch(new SuccessfulCommandEvent($input, $output, $command), CommandEvents::SUCCESSFUL);
    }

    public function wait(int $seconds = null): void
    {
        if(!$this->runningCommand) {
            if(!$seconds) {
                $seconds = 100;
            }

            $this->sleeper->sleep($seconds);

            return;
        }

        $this->runningCommand->wait();
    }

    protected function fail(InputInterface $input, OutputInterface $output, CommandInterface $command, int $exitCode): void
    {
        $event = new FailedCommandEvent($input, $output, $command, $exitCode);

        $this->dispatcher?->dispatch($event, CommandEvents::FAILED);
    }

    protected function except(InputInterface $input, OutputInterface $output, \Throwable $exception, CommandInterface $command = null): ExceptionConsoleEvent
    {
        $event = new ExceptionConsoleEvent($this, $input, $output, $exception, $command);

        $this->dispatcher?->dispatch($event, ConsoleEvents::EXCEPTION);

        return $event;
    }

    protected function throw(InputInterface $input, OutputInterface $output, \Throwable $exception, bool $throw): \Throwable
    {
        $event = $this->except($input, $output, $exception);
        $exception = $event->getThrowable();

        if($throw) {
            throw $exception;
        }

        return $exception;
    }

    protected function renderException(OutputInterface $output, \Throwable $exception): void
    {
        $message = trim($exception->getMessage());

        if ('' === $message || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $class = get_debug_type($exception);
            $title = sprintf('  [%s%s]  ', $class, 0 !== ($code = $exception->getCode()) ? ' ('.$code.')' : '');
            $length = Formatter::width($title);
        } else {
            $length = 0;
        }

        if (str_contains($message, "@anonymous\0")) {
            $message = preg_replace_callback('/[a-zA-Z_\x7f-\xff][\\\\a-zA-Z0-9_\x7f-\xff]*+@anonymous\x00.*?\.php(?:0x?|:[0-9]++\$)[0-9a-fA-F]++/', function ($m) {
                return class_exists($m[0], false) ? (get_parent_class($m[0]) ?: key(class_implements($m[0])) ?: 'class').'@anonymous' : $m[0];
            }, $message);
        }

        $width = $this->terminal->getWidth() ? $this->terminal->getWidth() - 1 : \PHP_INT_MAX;
        $lines = [];

        foreach ('' !== $message ? preg_split('/\r?\n/', $message) : [] as $line) {
            foreach($this->split($line, $width - 4) as $line) {
                // pre-format lines to get the right string length
                $lineLength = Formatter::width($line) + 4;
                $lines[] = [$line, $lineLength];

                $length = max($lineLength, $length);
            }
        }

        $messages = [];

        if (!$exception instanceof ExceptionInterface || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $messages[] = sprintf('<comment>%s</comment>', Formatter::escape(sprintf('In %s line %s:', basename($exception->getFile()) ?: 'n/a', $exception->getLine() ?: 'n/a')));
        }

        $messages[] = $emptyLine = sprintf('<error>%s</error>', str_repeat(' ', $length));

        if ('' === $message || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $messages[] = sprintf('<error>%s%s</error>', $title, str_repeat(' ', max(0, $length - Formatter::width($title))));
        }

        foreach ($lines as $line) {
            $messages[] = sprintf('<error>  %s  %s</error>', Formatter::escape($line[0]), str_repeat(' ', $length - $line[1]));
        }

        $messages[] = $emptyLine;
        $messages[] = '';

        $output->line()->write($messages, OutputInterface::VERBOSITY_QUIET);

        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->line()->write('<comment>Exception trace:</comment>', OutputInterface::VERBOSITY_QUIET);

            // exception related properties
            $trace = $exception->getTrace();

            array_unshift($trace, [
                'function' => '',
                'file' => $exception->getFile() ?: 'n/a',
                'line' => $exception->getLine() ?: 'n/a',
                'args' => [],
            ]);

            foreach ($trace as $value) {
                $class = $value['class'] ?? '';
                $type = $value['type'] ?? '';
                $function = $value['function'] ?? '';
                $file = $value['file'] ?? 'n/a';
                $line = $value['line'] ?? 'n/a';

                $output->line()->write(sprintf(' %s%s at <info>%s:%s</info>', $class, $function ? $type . $function.'()' : '', $file, $line), OutputInterface::VERBOSITY_QUIET);
            }

            $output->line()->write('', OutputInterface::VERBOSITY_QUIET);
        }
    }

    protected function terminate(int $exitCode, InputInterface $input, OutputInterface $output, CommandInterface $command): int
    {
        $this->lastCommand = $command;

        $event = new TerminateConsoleEvent($exitCode, $input, $output, $command);

        $this->dispatcher?->dispatch($event, ConsoleEvents::TERMINATE);

        return $event->getExitCode();
    }

    public function complete(CompletionInputInterface $input, CompletionSuggestionsInterface $suggestions): void
    {
        if (
            CompletionInputInterface::TYPE_ARGUMENT_VALUE === $input->getCompletionType()
            && 'command' === $input->getCompletionName()
        ) {
            $suggestions->suggestValues(array_filter(array_map(function (CommandInterface $command) {
                return $command->isHidden() ? null : $command->getName();
            }, $this->all())));

            return;
        }

        if (CompletionInputInterface::TYPE_OPTION_NAME === $input->getCompletionType()) {
            $suggestions->suggestOptions($this->getDefinition()->getOptions());

            return;
        }
    }

    protected function configure(InputInterface $input, OutputInterface $output): void
    {
        if (true === $input->hasParameterOption(['--no-interaction', '-n'], true)) {
            $input->setInteractivity(false);
        }

        switch ($shellVerbosity = (int) getenv('SHELL_VERBOSITY')) {
            case -1: $output->setVerbosity(OutputInterface::VERBOSITY_QUIET); break;
            case 1: $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE); break;
            case 2: $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE); break;
            case 3: $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG); break;
            default: $shellVerbosity = 0; break;
        }

        if (true === $input->hasParameterOption(['--quiet', '-q'], true)) {
            $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
            $shellVerbosity = -1;
        } else {
            if ($input->hasParameterOption(['-vvv'], true) || $input->hasParameterOption(['--verbose=3'], true) || 3 === $input->getParameterOption(['--verbose'], false, true)) {
                $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
                $shellVerbosity = 3;
            } elseif ($input->hasParameterOption(['-vv'], true) || $input->hasParameterOption(['--verbose=2'], true) || 2 === $input->getParameterOption(['--verbose'], false, true)) {
                $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
                $shellVerbosity = 2;
            } elseif ($input->hasParameterOption(['-v'], true) || $input->hasParameterOption(['--verbose=1'], true) || $input->hasParameterOption(['--verbose'], true) || $input->getParameterOption(['--verbose'], false, true)) {
                $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
                $shellVerbosity = 1;
            }
        }

        if (-1 === $shellVerbosity) {
            $input->setInteractivity(false);
        }

        if (\function_exists('putenv')) {
            @putenv('SHELL_VERBOSITY='.$shellVerbosity);
        }

        $_ENV['SHELL_VERBOSITY'] = $shellVerbosity;
        $_SERVER['SHELL_VERBOSITY'] = $shellVerbosity;
    }

    protected function split(string $string, int $width): array
    {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return str_split($string, $width);
        }

        $utf8String = mb_convert_encoding($string, 'utf8', $encoding);
        $lines = [];
        $line = '';

        $offset = 0;

        while (preg_match('/.{1,10000}/u', $utf8String, $m, 0, $offset)) {
            $offset += \strlen($m[0]);

            foreach (preg_split('//u', $m[0]) as $char) {
                if (mb_strwidth($line.$char, 'utf8') <= $width) {
                    $line .= $char;
                    continue;
                }

                $lines[] = str_pad($line, $width);
                $line = $char;
            }
        }

        $lines[] = \count($lines) ? str_pad($line, $width) : $line;

        mb_convert_variables($encoding, 'utf8', $lines);

        return $lines;
    }

    public function hasLastCommand(): bool
    {
        return $this->lastCommand instanceof CommandInterface;
    }

    public function getLastCommand(): ?CommandInterface
    {
        return $this->lastCommand;
    }

    public function getHelp(): string
    {
        return $this->getLongVersion();
    }

    public function setProviders(ProviderCollectionInterface $providers): self
    {
        $this->providers = $providers;

        return $this;
    }

    public function addProvider(ProviderInterface $provider): self
    {
        $this->providers->set($provider);

        return $this;
    }

    public function getProviders(): ProviderCollectionInterface
    {
        return $this->providers ?? $this->getDefaultProviders();
    }

    protected function getDefaultProviders(): ProviderCollectionInterface
    {
        return new ProviderCollection([
            new FormatterProvider(),
            new QuestionProvider($this->terminal)
        ]);
    }

    protected function getDefaultDefinition()
    {
        return new Definition([
            new Argument('command', Argument::REQUIRED, 'The command to execute'),
            new Option('--help', '-h', Option::VALUE_NONE, 'Display help for the given command. When no command is given display help for the <info> ' . $this->default_command . '</info> command'),
            new Option('--quiet', '-q', Option::VALUE_NONE, 'Do not output any message'),
            new Option('--verbose', '-v|vv|vvv', Option::VALUE_REQUIRED, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new Option('--version', '-V', Option::VALUE_NONE, 'Display this application version'),
            new Option('--ansi', '', Option::VALUE_NONE, 'Force (or disable --no-ansi) ANSI output', null),
            new Option('--no-interaction', '-n', Option::VALUE_NONE, 'Do not ask any interactive question'),
        ]);
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return CommandInterface[]
     */
    protected function getDefaultCommands()
    {
        return [
            new HelpCommand(),
            new ListCommand(),
            new CompleteCommand(),
            new DumpCompletionCommand(),
        ];
    }

}