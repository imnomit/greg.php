<?php

namespace nomit\Console\Command;

use nomit\Dumper\Dumper;
use Psr\Log\LoggerInterface;
use nomit\Console\Command\Command;
use nomit\Console\Completion\CompletionInputInterface;
use nomit\Console\Completion\CompletionSuggestionsInterface;
use nomit\Console\Definition\Argument\Argument;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Definition\Option\Option;
use nomit\Console\Definition\Option\OptionInterface;
use nomit\Console\Descriptor\ConsoleDescription;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Console\Provider\DescriptorProvider;
use nomit\Process\Action\ActionInterface;
use nomit\Process\ProcessInterface;
use nomit\Utility\Bag\BagInterface;
use function nomit\dump;

class ListCommand extends Command
{

    public static string $default_name = '|list';

    public function __construct()
    {
        parent::__construct('list');
    }

    public function configure(): void
    {
        $this
            ->setName('list')
            ->setDefinition([
                new Argument('command', ArgumentInterface::OPTIONAL, 'The command name.'),
                new Argument('namespace', ArgumentInterface::OPTIONAL, 'The namespace name', null, function () {
                    return array_keys((new ConsoleDescription($this->getConsole()))->getNamespaces());
                }),
                new Option('raw', null, OptionInterface::VALUE_NONE, 'To output raw command list'),
                new Option('format', null, OptionInterface::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt', function () {
                    return (new DescriptorProvider())->getFormats();
                }),
                new Option('short', null, OptionInterface::VALUE_NONE, 'To skip describing commands\' arguments'),
            ])
            ->setDescription('Lists the available commands.')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command lists all commands:
  <info>%command.full_name%</info>
You can also display the commands for a specific namespace:
  <info>%command.full_name% test</info>
You can also output the information in other formats by using the <comment>--format</comment> option:
  <info>%command.full_name% --format=xml</info>
It's also possible to get raw list of commands (useful for embedding command runner):
  <info>%command.full_name% --raw</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = new DescriptorProvider();

        $provider->describe($output, $this->getConsole(), [
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
            'namespace' => $input->getArgument('namespace'),
            'short' => $input->getOption('short'),
        ]);

        return 0;
    }

    public function complete(CompletionInputInterface $input, CompletionSuggestionsInterface $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('namespace')) {
            $descriptor = new ConsoleDescription($this->getConsole());

            $suggestions->suggestValues(array_keys($descriptor->getNamespaces()));

            return;
        }

        if ($input->mustSuggestOptionValuesFor('format')) {
            $provider = new DescriptorProvider();

            $suggestions->suggestValues($provider->getFormats());
        }
    }

}