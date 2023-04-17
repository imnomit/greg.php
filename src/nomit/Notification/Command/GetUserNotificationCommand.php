<?php

namespace nomit\Notification\Command;

use nomit\Console\Command\Command;
use nomit\Console\Definition\Argument\Argument;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Console\Provider\Question\Question;
use nomit\Console\Provider\Table\TableSeparator;
use nomit\Dumper\Dumper;
use nomit\Notification\Notification\NotificationInterface;
use nomit\Notification\NotifierInterface;
use nomit\Notification\Stamp\StampInterface;
use nomit\Security\Authentication\Exception\NotFoundUserException;
use nomit\Security\User\UserInterface;
use nomit\Utility\Arrays;

final class GetUserNotificationCommand extends Command
{

    public function __construct(
        private NotifierInterface $notifier,
        private array $userProviders
    )
    {
        parent::__construct('notification:user');
    }

    public function configure(): void
    {
        $this->setName('notification:user')
            ->setDescription('Returns the notifications of the specified user.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command returns in a tabular format the notifications of a specified user.
EOT
            )
            ->setDefinition([
                new Argument('userId', ArgumentInterface::REQUIRED, 'The user ID of the user the notifications of which are to be returned.'),
            ]);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new ConsoleStyle($input, $output);

        $style->section(sprintf('Welcome to the <info>%s</> notification command.', $this->getName()));

        if(($userId = $input->getArgument('userId')) === null) {
            $userIdQuestion = new Question('Please enter the user ID of the user the notifications of which you wish to be returned.');
            $userIdQuestion->setNormalizer(function($userId) {
                return (int) $userId;
            });

            $userId = $style->askQuestion($userIdQuestion);
        }

        $user = $this->getUser($userId);

        if(!$user) {
            throw new NotFoundUserException($userId);
        }

        try {
            if(($response = $this->notifier->load($user)) !== null) {
                $notifications = $response->getNotifications();

                $style->inform(sprintf('In total, <info>%s</> notifications were returned for the user with the user ID "%s".', count($notifications), $userId));

                $rows = array_map(function(NotificationInterface $notification) {
                    $stamps = array_map(function(array $stamps) {
                        return (reset($stamps))->getName();
                    }, $notification->all());

                    return [
                        $notification->getMessage()->getTitle(),
                        $notification->getMessage()->toString(),
                        implode(', ', $stamps)
                    ];
                }, $notifications);

                Arrays::unshift($rows, new TableSeparator());
                Arrays::push($rows, new TableSeparator());

                $style->table(['Title', 'Message', 'Stamps'], $rows);
            } else {
                $style->warn(sprintf('No notifications were found for the user with the user ID <info>%s</>.', $userId));
            }

            return self::RESULT_SUCCESS;
        } catch(\Throwable $exception) {
            $style->error(sprintf('An error, code <info>%s</>, occurred in <info>%s</> at line <info>%s</> while attempting to load the notifications of the user with the user ID <info>%s</> and render them into a table: <info>%s</>.', $exception->getCode(), $exception->getFile(), $exception->getLine(), $userId, $exception->getMessage()));

            return self::RESULT_FAILURE;
        }
    }

    private function getUser(int $userId): ?UserInterface
    {
        $provider = current($this->userProviders);

        return $provider->getByUserId($userId);
    }

}