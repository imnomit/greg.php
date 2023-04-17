<?php

namespace nomit\Console\Descriptor;

use nomit\Console\Command\CommandInterface;
use nomit\Console\ConsoleInterface;
use nomit\Console\Exception\UnresolvableCommandConsoleException;

class ConsoleDescription implements DescriptionInterface
{

    public const GLOBAL_NAMESPACE = '_global';

    protected ConsoleInterface $console;

    protected ?string $namespace;

    protected bool $show_hidden;

    protected array $namespaces;

    protected array $commands;

    protected array $aliases = [];

    public function __construct(ConsoleInterface $console, string $namespace = null, bool $showHidden = false)
    {
        $this->console = $console;
        $this->namespace = $namespace;
        $this->show_hidden = $showHidden;
    }

    public function getNamespaces(): array
    {
        if(!isset($this->namespaces)) {
            $this->inspectConsole();
        }

        return $this->namespaces;
    }

    public function getCommands(): array
    {
        if(!isset($this->commands)) {
            $this->inspectConsole();
        }

        return $this->commands;
    }

    public function getCommand(string $name): CommandInterface
    {
        if(!isset($this->commands[$name]) && !isset($this->aliases[$name])) {
            throw new UnresolvableCommandConsoleException(sprintf('No console command named "%s" exists.', $name));
        }

        return $this->commands[$name] ?? $this->aliases[$name];
    }

    protected function inspectConsole(): void
    {
        $this->commands = [];
        $this->namespaces = [];

        $all = $this->console->all($this->namespace ? $this->console->resolveNamespace($this->namespace) : null);

        foreach ($this->sortCommands($all) as $namespace => $commands) {
            $names = [];

            /** @var CommandInterface $command */
            foreach ($commands as $name => $command) {
                if (!$command->getName() || (!$this->show_hidden && $command->isHidden())) {
                    continue;
                }

                if ($command->getName() === $name) {
                    $this->commands[$name] = $command;
                } else {
                    $this->aliases[$name] = $command;
                }

                $names[] = $name;
            }

            $this->namespaces[$namespace] = ['id' => $namespace, 'commands' => $names];
        }
    }

    protected function sortCommands(array $commands): array
    {
        $namespacedCommands = [];
        $globalCommands = [];
        $sortedCommands = [];

        foreach ($commands as $name => $command) {
            $key = $this->console->extractNamespace($name, 1);

            if (\in_array($key, ['', self::GLOBAL_NAMESPACE], true)) {
                $globalCommands[$name] = $command;
            } else {
                $namespacedCommands[$key][$name] = $command;
            }
        }

        if ($globalCommands) {
            ksort($globalCommands);

            $sortedCommands[self::GLOBAL_NAMESPACE] = $globalCommands;
        }

        if ($namespacedCommands) {
            ksort($namespacedCommands);

            foreach ($namespacedCommands as $key => $commandsSet) {
                ksort($commandsSet);
                $sortedCommands[$key] = $commandsSet;
            }
        }

        return $sortedCommands;
    }

}