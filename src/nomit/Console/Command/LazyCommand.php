<?php

namespace nomit\Console\Command;

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
use nomit\Console\Provider\ProviderInterface;
use nomit\Process\Action\ActionInterface;
use nomit\Process\ProcessInterface;
use nomit\Utility\Bag\BagInterface;

final class LazyCommand extends Command
{

    protected $command;

    protected bool $is_enabled;

    public function __construct(string $name, array $aliases, string $description, bool $isHidden,
                                \Closure $commandFactory, ?bool $isEnabled = true
    )
    {
        $this->setName($name)
            ->setAliases($aliases)
            ->setHidden($isHidden)
            ->setDescription($description);

        $this->command = $commandFactory;
        $this->is_enabled = $isEnabled;
    }

    public function setConsole(ConsoleInterface $console): self
    {
        if($this->command instanceof parent) {
            $this->command->setConsole($console);
        }

        return parent::setConsole($console);
    }

    public function setProviders(ProviderCollectionInterface $providers): self
    {
        if($this->command instanceof parent) {
            $this->command->setProviders($providers);
        }

        parent::setProviders($providers);
    }

    public function isEnabled(): bool
    {
        return $this->is_enabled ?? $this->getCommand()->isEnabled();
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        return $this->getCommand()->run($input, $output);
    }

    public function complete(CompletionInputInterface $input, CompletionSuggestionsInterface $suggestions): void
    {
        $this->getCommand()->complete($input, $suggestions);
    }

    public function setStatus(int $status): Command
    {
        $this->getCommand()->setStatus($status);
    }

    public function mergeDefinitions(bool $mergeArguments = true): void
    {
        $this->getCommand()->mergeDefinitions($mergeArguments);
    }

    public function setDefinition(array|DefinitionInterface $definition): Command
    {
        $this->getCommand()->setDefinition($definition);

        return $this;
    }

    public function getDefinition(): DefinitionInterface
    {
        return $this->getCommand()->getDefinition();
    }

    public function addArgument(ArgumentInterface $argument): Command
    {
        $this->getCommand()->addArgument($argument);

        return $this;
    }

    public function addOption(OptionInterface $option): Command
    {
        return $this->getCommand()->addOption($option);
    }

    public function setTitle(string $title): Command
    {
        return $this->getCommand()->setTitle($title);
    }

    public function getHelp(): ?string
    {
        return $this->getCommand()->getHelp();
    }

    public function getSynopsis(bool $short = false): string
    {
        return $this->getCommand()->getSynopsis($short);
    }

    public function addUsage(string $usage): Command
    {
        $this->getCommand()->addUsage($usage);

        return $this;
    }

    public function getProvider(string $name): ProviderInterface
    {
        return $this->getCommand()->getProvider($name);
    }

    public function getCommand(): parent
    {
        if (!$this->command instanceof \Closure) {
            return $this->command;
        }

        $command = $this->command = ($this->command)();
        $command->setApplication($this->getConsole());

        if (null !== $this->getProviders()) {
            $command->setHelperSet($this->getProviders());
        }

        $command->setName($this->getName())
            ->setAliases($this->getAliases())
            ->setHidden($this->isHidden())
            ->setDescription($this->getDescription());

        // Will throw if the command is not correctly initialized.
        $command->getDefinition();

        return $command;
    }

}