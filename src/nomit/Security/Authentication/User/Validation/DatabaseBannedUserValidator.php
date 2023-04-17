<?php

namespace nomit\Security\User\Authentication\Validation;

use nomit\Database\ConnectionInterface;
use nomit\Database\ExplorerInterface;
use nomit\Database\Table\ActiveRow;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Security\Authentication\User\Validation\Event\BannedUserEvent;
use nomit\Security\User\UserInterface;

final class DatabaseBannedUserValidator implements ValidatorInterface
{

    public function __construct(
        private ExplorerInterface $explorer,
        private ?EventDispatcherInterface $dispatcher = null,
        private string $table = 'user_bans'
    )
    {
    }

    public function validate(UserInterface $user): bool
    {
        $banData = $this->getBan($user);

        if(!$banData) {
            return true;
        }

        $ban = new Ban(
            $banData->user_id,
            $banData->author_user_id,
            new \DateTime($banData->datetime),
            $banData->reason
        );

        if($ban->getDateTime() > new \DateTime('NOW')) {
            $this->dispatcher?->dispatch(new BannedUserEvent($user, $ban));

            return false;
        }

        return true;
    }

    private function getBan(UserInterface $user): ?ActiveRow
    {
        return $this->explorer
            ->table($this->table)
            ->where('user_id', $user->getUserId())
            ->order('datetime DESC')
            ->fetch();
    }

    public function getMessage(): string
    {
        return 'The user with the username "%s" is currently subject to a ban, and so cannot sign in.';
    }

}