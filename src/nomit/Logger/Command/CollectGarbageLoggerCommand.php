<?php

namespace nomit\Logger\Command;

use nomit\Console\Command\Command;
use nomit\Console\Definition\Argument\Argument;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Console\Provider\Question\Question;
use nomit\FileSystem\Directory\Directory;
use nomit\FileSystem\Exception\ExceptionInterface;
use nomit\FileSystem\FileSystem;
use nomit\Lock\LockFactoryInterface;
use nomit\Lock\LockInterface;
use Psr\Log\LoggerInterface;

final class CollectGarbageLoggerCommand extends Command
{

    public function __construct(
        private FileSystem $fileSystem,
        private string $logDirectory,
        private LockFactoryInterface $lockFactory,
        private string $lockKey = '_logger.gc.lock'
    )
    {
        parent::__construct('logger:gc');
    }

    public function configure(): void
    {
        $this->setName('logger:gc')
            ->setDescription('Removes log files that exceed the configured time-to-live.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command removes log files exceeding the configured time-to-live.
EOT
            )
            ->setDefinition([
                new Argument('ttl', ArgumentInterface::REQUIRED, 'The time-to-live, in seconds, beyond which log files will be removed.')
            ]);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new ConsoleStyle($input, $output);

        $lock = $this->getLock();

        if($lock->isAcquired()) {
            $style->error('A lock has already been acquired on the log garbage-collection process.');

            return self::RESULT_FAILURE;
        }

        $lock->acquire(true);

        try {
            if(($ttl = $input->getArgument('ttl')) === null) {
                $ttlQuestion = new Question('Please enter the maximum time-to-live beyond which log files will be removed.', 86400);
                $ttlQuestion->setNormalizer(function(string $ttl) {
                    return (int) $ttl;
                });

                $ttl = $style->askQuestion($ttlQuestion);
            }

            if(!FileSystem::isDirectory($this->logDirectory)) {
                $this->fileSystem->makeDirectory($this->logDirectory);
            }

            $directory = new Directory($this->logDirectory);

            $style->inform(sprintf('All log files with a lifetime exceeding that of the configured time-to-live maximum, <info>%s</>, will be removed.', $ttl));

            foreach($directory as $file) {
                if($file->isDot() || $file->isLink()) {
                    continue;
                }

                $createdTime = $file->getCTime();

                if($createdTime + $ttl < time()) {
                    try {
                        $this->fileSystem->remove($pathname = $file->getPathname());

                        $style->success(sprintf('The log file with the pathname <info>%s</> and a creation timestamp of <info>%s</> has been successfully removed.', $pathname, $createdTime));
                    } catch (ExceptionInterface $exception) {
                        $style->error(sprintf('An error, code <info>%s</>, occurred in <info>%s</> at line <info>%s</> while attempting to remove the log file with the pathname <info>%s</>: <info>%s</>.', $exception->getCode(), $exception->getFile(), $exception->getLine(), $pathname, $exception->getMessage()));

                        return self::RESULT_FAILURE;
                    }
                }
            }

            return self::RESULT_SUCCESS;
        } finally {
            $lock->release();

            $style->inform(sprintf('The <info>%s</> log-removal process has been completed and is thus being terminated.', $this->getName()));
        }
    }

    private function getLock(): LockInterface
    {
        return $this->lockFactory->createLock($this->lockKey);
    }

}