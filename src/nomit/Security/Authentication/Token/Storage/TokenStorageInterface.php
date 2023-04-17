<?php

namespace nomit\Security\Authentication\Token\Storage;

use nomit\Security\Authentication\Token\TokenInterface;

interface TokenStorageInterface
{

    public function setToken(?TokenInterface $token): self;

    public function hasToken(): bool;

    public function getToken(): ?TokenInterface;

    public function removeToken(): void;

}