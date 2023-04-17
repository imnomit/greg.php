<?php

namespace nomit\Security\Authentication\EventListener;

use nomit\Cryptography\Password\PasswordHasherFactoryInterface;
use nomit\Dumper\Dumper;
use nomit\EventDispatcher\AbstractEventSubscriber;
use nomit\EventDispatcher\EventSubscriberInterface;
use nomit\Security\Authentication\Event\CheckPassportAuthenticationEvent;
use nomit\Security\Authentication\Exception\AuthenticationException;
use nomit\Security\Authentication\Exception\BadCredentialsException;
use nomit\Security\Authentication\Passport\Badge\Credentials\Credentials;
use nomit\Security\Authentication\Passport\Badge\Credentials\CredentialsInterface;
use nomit\Security\Authentication\Passport\Badge\PasswordUpgraderBadge;
use nomit\Security\Authentication\Passport\UserPassportInterface;
use nomit\Security\Authentication\User\UserProviderInterface;
use nomit\Security\User\PasswordAuthenticatedUserInterface;

final class EvaluateCredentialsEventListener extends AbstractEventSubscriber
{

    public function __construct(
        private PasswordHasherFactoryInterface $factory,
        private UserProviderInterface $userProvider,
    )
    {
    }

    public function onCheckPassport(CheckPassportAuthenticationEvent $event): void
    {
        $passport = $event->getPassport();

        if($passport instanceof UserPassportInterface && $passport->hasBadge(Credentials::class)) {
            $user = $passport->getUser();

            if(!$user instanceof PasswordAuthenticatedUserInterface) {
                throw new AuthenticationException(sprintf('The user class of the passport "%s" must implement the "%s" interface.', get_class($passport), PasswordAuthenticatedUserInterface::class));
            }

            /**
             * @var CredentialsInterface $badge
             */
            $badge = $passport->getBadge(Credentials::class);

            if($badge->isResolved()) {
                return;
            }

            $presentedPassword = $badge->getPassword();

            if(empty($presentedPassword)) {
                throw new BadCredentialsException('The user password must not be empty.');
            }

            $salt = method_exists($user, 'getSalt') ? $user->getSalt() : '';
            $passwordHasher = $this->factory->getPasswordHasher($user);
            $userPassword = $user->getPassword();

            if(!$passwordHasher->verify($userPassword, $presentedPassword, $salt)) {
                throw new BadCredentialsException('The supplied user account password is incorrect. Please verify your login credentials and try again.');
            }

            if($passwordHasher->needsRehash($userPassword)) {
                $hashedPassword = $passwordHasher->hash($presentedPassword);

                $user->setPassword($hashedPassword);

                if(!$this->userProvider->save($user)) {
                    $user->setCredentialsExpired(true);
                }
            }

            $badge->resolve();

            if(!$passport->hasBadge(PasswordUpgraderBadge::class)) {
                $passport->addBadge(new PasswordUpgraderBadge($presentedPassword));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportAuthenticationEvent::class => 'onCheckPassport'
        ];
    }

}