<?php

namespace nomit\Security\PasswordReset\Mail;

use nomit\Mail\Message\MessageInterface;
use nomit\Security\PasswordReset\Token\TokenInterface;

interface MailerInterface
{

    public function setToken(TokenInterface|string $token): self;

    public function getToken(): ?string;

    public function setMessage(MessageInterface $message): self;

    public function getMessage(): ?MessageInterface;

    public function send(string $to, string $from, string $subject): void;

}