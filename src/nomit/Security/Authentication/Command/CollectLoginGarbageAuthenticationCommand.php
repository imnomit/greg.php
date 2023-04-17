<?php

namespace nomit\Security\Authentication\Command;

use nomit\Console\Command\Command;
use nomit\Console\Definition\Argument\Argument;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Console\Provider\Question\Question;
use nomit\Security\Authentication\Token\Persistence\TokenPersistenceInterface;

final class CollectLoginGarbageAuthenticationCommand extends Command
{

    public function __construct(
        private TokenPersistenceInterface $tokenPersistence,
    )
    {
        parent::__construct('auth:gc');
    }

    public function configure(): void
    {
        $this->setName('auth:gc')
            ->setDescription('Removes all authentication login records exceeding a time-to-live threshold.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> removes all authentication login records exceeding a time-to-live threshold.
EOT
            )
            ->setDefinition([
                new Argument('ttl', ArgumentInterface::REQUIRED, 'The maximum time-to-live of the authentication login records.'),
            ]);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new ConsoleStyle($input, $output);

        $style->section(sprintf('Welcome to the "%s" authentication command.', $this->getName()));

        if(($ttl = $input->getArgument('ttl')) === null) {
            $ttlQuestion = new Question('Please enter the maximum time-to-live beyond which authentication login records should be removed.');
            $ttlQuestion->setNormalizer(function(string $item) {
                return (int) $item;
            });

            $ttl = $style->askQuestion($ttlQuestion);
        }

        if(!$style->confirm('Do you wish to begin the authentication login record garbage collection process?')) {
            $style->inform('You have opted to abort the authentication login record garbage collection process.');

            return self::RESULT_SUCCESS;
        }

        try {
            if($this->tokenPersistence->collectGarbage($ttl)) {
                $style->success('The authentication login record garbage has been successfully collected, and all expired records removed.');
            } else {
                $style->warn('An error occurred while attempting to collect the authentication login record garbage and remove one or more expired records.');
            }
        } catch(\Throwable $exception) {
            $style->error(sprintf('An error, code <info>%s</>, occurred in <info>%s</> at line <info>%s</> while attempting to collect the authentication login record garbage: <info>%s</>.', $exception->getCode(), $exception->getFile(), $exception->getLine(), $exception->getMessage()));

            return self::RESULT_FAILURE;
        } finally {
            $style->inform(sprintf('The <info>%s</> authentication command has completed running and is being terminated.', $this->getName()));
        }

        return self::RESULT_SUCCESS;
    }

}