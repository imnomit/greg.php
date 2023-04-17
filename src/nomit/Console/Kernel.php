<?php

namespace nomit\Console;

use nomit\Bootstrap\Configurator;
use nomit\Console\Command\CommandInterface;
use nomit\Console\Definition\Option\Option;
use nomit\Console\Exception\ExceptionInterface;
use nomit\Console\Exception\LogicException;
use nomit\Console\Format\Formatter;
use nomit\Console\Format\FormatterInterface;
use nomit\Console\Format\OutputFormatter;
use nomit\Console\Format\OutputFormatterInterface;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\DependencyInjection\ContainerAwareInterface;
use nomit\Dumper\Dumper;
use Psr\Container\ContainerInterface;

class Kernel extends Console implements KernelInterface
{

    private bool $registered = false;

    private array $registrationErrors = [];

    public function __construct(
        protected ?ContainerInterface $container,
        private array $serviceIds = [],
    )
    {
        parent::__construct('nomit', Configurator::VERSION);

        $definition = $this->getDefinition();

        $definition->addOption(new Option('--env', '-e', Option::VALUE_REQUIRED, 'The Environment name.', $container->getEnvironment()));
        $definition->addOption(new Option('--no-debug', null, Option::VALUE_NONE, 'Switch off debug mode.'));
    }

    public function dispatch(InputInterface $input, OutputInterface $output): int
    {
        $this->register();

        $this->configure($input, $output);

        if($this->registrationErrors) {
            $this->renderRegistrationErrors($input, $output);
        }

        if(!$this->dispatcher) {
            $this->setEventDispatcher($this->container->get('event_dispatcher'));
        }

        return parent::dispatch($input, $output);
    }

    public function resolve(string $name): CommandInterface
    {
        $this->register();

        return parent::resolve($name);
    }

    public function get(string $name): CommandInterface
    {
        $this->register();

        $command = parent::get($name);

        if($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->container);
        }

        return $command;
    }

    public function all(string $namespace = null): array
    {
        $this->register();

        return parent::all($namespace);
    }

    public function add(CommandInterface $command): ?CommandInterface
    {
        $this->register();

        return parent::add($command);
    }

    private function register(): void
    {
        if($this->registered) {
            return;
        }

        $container = $this->container;

        if($container->has('console.command.repository')) {
            $this->setCommandRepository($container->get('console.command.repository'));
        } else {
            throw new LogicException(sprintf('The "%s" console is missing a command repository object with which to work.', __CLASS__));
        }

        $this->registered = true;
    }

    private function renderRegistrationErrors(InputInterface $input, OutputInterface $output): void
    {
        (new ConsoleStyle($input, $output, $this->getFormatter()))->warn('Some commands could not be registered:');

        foreach ($this->registrationErrors as $error) {
            $this->renderException($output, $error);
        }
    }

    protected function configure(InputInterface $input, OutputInterface $output): void
    {
        parent::configure($input, $output);

        $inputFactory = $this->container->get('console.input.factory');
        $outputFactory = $this->container->get('console.output.factory');

        $inputFactory->factory($input);
        $outputFactory->factory($output);
    }

    private function getFormatter(): OutputFormatterInterface
    {
        return $this->container->has($id = 'console.formatter.output') ? $this->container->get($id) : new OutputFormatter();
    }

}