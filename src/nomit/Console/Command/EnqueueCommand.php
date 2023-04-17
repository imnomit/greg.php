<?php

namespace nomit\Console\Command;

use nomit\Console\Definition\Argument\Argument;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Definition\Option\Option;
use nomit\Console\Definition\Option\OptionInterface;
use nomit\Console\Exception\ExceptionInterface;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;

final class EnqueueCommand extends QueueCommand
{

    private ?CommandInterface $command = null;

    public function __construct(
        array $queues
    )
    {
        $this->queues = $queues;

        parent::__construct('queue:enqueue');
    }

    public function configure(): void
    {
        $this->setDefinition([
            new Argument('command_name', ArgumentInterface::REQUIRED, 'The command name.'),
            new Argument('queue_name', ArgumentInterface::REQUIRED, 'The subject queue\'s name.'),
            new Option('frequency', null, OptionInterface::VALUE_REQUIRED, 'The frequency with which to run the specified command.')
        ])
            ->setDescription('Enqueue a command to the console command queue.');
    }

    public function setCommand(CommandInterface $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if(null === $this->command) {
            $this->command = $this->getConsole()->resolve($input->getArgument('command_name'));
        }

        $style = new ConsoleStyle($input, $output);
        $queueName = $input->getArgument('queue_name');
        $queue = $this->getQueue($queueName);
        $commandName = $this->command->getName();
        $frequency = $this->command->getOption('frequency') ?? '* * * * *';

        $style->inform(sprintf('Starting enqueuing of command named "%s" to the queue named "%s".', $commandName, $queueName));

        try {
            $this->command->at($frequency->getValue());

            $queue->enqueue($this->command);

            $style->success(sprintf('The specified command, "%s", has been successfully enqueued to the console command queue named "%s".', $commandName, $queueName));
        } catch(ExceptionInterface $exception) {
            $style->error(sprintf('An error occurred while attempting to enqueue the specified command, named "%s", to the console command queue named "%s": "%s".', $commandName, $queueName, $exception->getMessage()));
        } catch(\Throwable $exception) {
            $style->error(sprintf('An unexpected error occurred while attempting to enqueue the specified command, named "%s", to the console command queue named "%s": "%s".', $commandName, $queueName, $exception->getMessage()));

            throw $exception;
        } finally {
            $style->inform(sprintf('Finishing enqueing process of the command named "%s" to the console command queue named "%s".', $commandName, $queueName));
        }

        return 0;
    }


}