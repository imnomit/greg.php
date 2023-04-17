<?php

namespace nomit\Notification\Command;

use nomit\Console\Command\Command;
use nomit\Console\Definition\Argument\Argument;
use nomit\Console\Definition\Argument\ArgumentInterface;
use nomit\Console\Format\Style\ConsoleStyle;
use nomit\Console\Input\InputInterface;
use nomit\Console\Output\OutputInterface;
use nomit\Console\Provider\Question\ConfirmationQuestion;
use nomit\Console\Provider\Question\Question;
use nomit\Notification\Message\Message;
use nomit\Notification\Message\ParameterMessage;
use nomit\Notification\Notification\Notification;
use nomit\Notification\NotifierInterface;
use nomit\Notification\Stamp\CommandStamp;
use nomit\Security\Authentication\User\UserProviderInterface;
use Psr\Log\LoggerInterface;

final class PushToEveryoneNotificationCommand extends Command
{

    /**
     * PushToEveryoneNotificationCommand constructor.
     * @param UserProviderInterface[] $userProviders
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        private NotifierInterface $notifier,
        private array $userProviders,
    )
    {
        parent::__construct('notification:everyone');
    }

    public function configure(): void
    {
        $this->setDescription('Pushes to all user accounts a new notification.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command pushes to all the users of the supplied user providers a newly-created notification.
EOT
)
            ->setDefinition([
                new Argument('title', ArgumentInterface::REQUIRED, 'The title of the notification.'),
                new Argument('message', ArgumentInterface::OPTIONAL, 'The message body of the notification.'),
            ]);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new ConsoleStyle($input, $output);

        $style->section(sprintf('Welcome to the <info>%s</> notification command.', $this->getName()));

        $style->inform('The following questions will guide you through the process of creating the notification to be pushed to all the users of the supplied user providers.');

        if(!$style->confirm('Do you wish to begin creating the notification to be pushed to all users?', true)) {
            $style->inform('You have opted not to create a notification to be pushed to all site users. Good-bye!');

            return self::RESULT_SUCCESS;
        }

        if($input->hasArgument('title')) {
            $title = $input->getArgument('title');
        } else {
            $title = $style->ask('What should the title of the notification be?');
        }

        if($input->hasArgument('message')) {
            $message = $input->getArgument('message');
        } else {
            $message = $style->ask('What should the message of the notification be?');
        }

        $parameters = [];
        
        if(preg_match_all('/{+(.*?)}/', $message, $matches)) {
            foreach($matches as $match) {
                $parameters[$match] = $style->ask(sprintf('Enter a value for the parameter keyed <info>%s</>.', $match));
            }
        }

        if(count($parameters) > 0) {
            $message = new ParameterMessage($title, $message, $parameters);
        } else {
            $message = new Message($title, $message);
        }

        $notification = new Notification($message, [new CommandStamp($this->getName())]);

        if(!$style->confirm(sprintf('Do you wish to send to all site users the notification titled <info>%s</> with a message body of <info>%s</>?', $title, $message), true)) {
            $style->inform('You have opted not to send the created notification to all site users. Good-bye!');

            return self::RESULT_SUCCESS;
        }

        try {
            foreach($this->getUsers() as $user) {
                if($this->notifier->add($user, $notification)) {
                    $style->success(sprintf('The created notification has been successfully sent to the user with the user ID <info>%s</>.', $user->getUserId()));
                } else {
                    $style->warn(sprintf('An error occurred while attempting to send the created notification to the user with the user ID <info>%s</>.', $user->getUserId()));
                }
            }

            $style->success('The created notification has been successfully sent to all site users.');
        } catch(\Throwable $exception) {
            $style->error(sprintf('An error, code <info>%s</>, occurred in <info>%s</> at line <info>%s</> while attempting to send the created notification to all site users: <info>%s</>.', $exception->getCode(), $exception->getFile(), $exception->getLine(), $exception->getMessage()));

            return self::RESULT_FAILURE;
        } finally {
            $style->inform(sprintf('The <info>%s</> notification command has completed running and is being terminated.', $this->getName()));
        }

        return self::RESULT_SUCCESS;
    }

    private function getUsers(): array
    {
        $users = [];

        foreach($this->userProviders as $userProvider) {
            $providerUsers = $userProvider->all();

            foreach($providerUsers as $providerUser) {
                $users[$providerUser->getUserId()] = $providerUser;
            }
        }

        return $users;
    }

}