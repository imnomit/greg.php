<?php

namespace nomit\Security\Registration\Token;

use nomit\Security\Registration\RegistrationInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Stringable;

interface TokenInterface extends Arrayable, Stringable
{

    public const PAYLOAD_DELIMITER = '~~';

    public function setRegistration(RegistrationInterface $registration): self;

    public function getRegistration(): RegistrationInterface;

    public function setExpiry(\DateTimeInterface $dateTime): self;

    public function getExpiry(): \DateTimeInterface;

}