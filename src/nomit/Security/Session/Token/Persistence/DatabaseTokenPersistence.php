<?php

namespace nomit\Security\Session\Token\Persistence;

use nomit\Console\Format\Style\ConsoleStyleInterface;
use nomit\Console\Format\Style\StyleInterface;
use nomit\Cryptography\EncrypterInterface;
use nomit\Cryptography\Hasher\HasherInterface;
use nomit\Database\ConnectionInterface;
use nomit\Database\ExplorerInterface;
use nomit\Dumper\Dumper;
use nomit\Lock\LockFactoryInterface;
use Psr\Container\ContainerInterface;
use nomit\Exception\ServiceNotFoundException;
use nomit\Security\Session\Token\TokenInterface;

class DatabaseTokenPersistence extends AbstractTokenPersistence
{

    public function __construct(
        private ConnectionInterface $connection,
        private ExplorerInterface $explorer,
        EncrypterInterface $encrypter,
        HasherInterface $hasher,
        LockFactoryInterface $lockFactory,
        private string $table = 'session_tokens'
    )
    {
        parent::__construct($encrypter, $hasher, $lockFactory);
    }

    public function getDatabase(): ConnectionInterface
    {
        return $this->connection;
    }

    public function getExplorer(): ExplorerInterface
    {
        return $this->explorer;
    }

    /**
     * @param string $sessionId
     * @return bool
     */
    public function hasToken(string $sessionId): bool
    {
        return $this->getExplorer()->table($this->table)
                ->where('session_id', $sessionId)
                ->count() > 0;
    }

    /**
     * @param string $sessionId
     * @return TokenInterface|null
     */
    public function loadToken(string $sessionId): ?TokenInterface
    {
        $lock = $this->getLock($sessionId);

        $lock->acquire(true);

        try {
            $tokenData = $this->getExplorer()->table($this->table)
                ->where('session_id', $sessionId)
                ->fetch();

            if(!$tokenData) {
                return null;
            }

            return $this->unserializeToken($tokenData->toArray());
        } finally {
            $lock->release();
        }
    }

    /**
     * @param TokenInterface $token
     * @return bool
     */
    public function saveToken(TokenInterface $token): bool
    {
        $lock = $this->getLock($token->getSessionId());

        $lock->acquire(true);

        try {
            $data = $this->serializeToken($token);

            if($this->hasToken($token->getSessionId()) === false) {
                $query = $this->getDatabase()
                    ->query('INSERT INTO ?name', $this->table, $data);
            } else {
                $query = $this->getDatabase()
                    ->query('UPDATE ?name SET', $this->table, $data, 'WHERE ?', [
                        'session_id' => $data['session_id']
                    ]);
            }

            return $query->getRowCount() > 0;
        } finally {
            $lock->release();
        }
    }

    /**
     * @param string $sessionId
     * @return bool
     */
    public function deleteToken(string $sessionId): bool
    {
        $lock = $this->getLock($sessionId);

        $lock->acquire(true);

        try {
            return $this->getDatabase()
                    ->query('DELETE FROM ?name WHERE ?', $this->table, [
                        'session_id' => $sessionId
                    ])->getRowCount() > 0;
        } finally {
            $lock->release();
        }
    }

    /**
     * @param int $maximumLifetime
     * @return bool
     * @throws \Exception
     */
    public function collectGarbage(int $maximumLifetime, ConsoleStyleInterface $style = null): bool
    {
        $timestamp = (new \DateTime('NOW'))->getTimestamp() - $maximumLifetime;
        $datetime = new \DateTime('@' . $timestamp);

        $count = $this->getDatabase()
                ->query('DELETE FROM ?name WHERE ?', $this->table, [
                    'created_at <' => $datetime
                ])->getRowCount();

        if($count > 0) {
            $style->success(sprintf('<>%s</> session token persitsence records were successfully deleted.', $count));
        } else {
            $style->warn(sprintf('No session token persitence records were deleted, most likely because no records were found with a lifetime greater than the configured lifetime, <info>%s</>.', $datetime->format('U')));
        }

        return $count > 0;
    }

}