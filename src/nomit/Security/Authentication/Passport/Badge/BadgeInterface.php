<?php

namespace nomit\Security\Authentication\Passport\Badge;

interface BadgeInterface
{

    public function isResolved(): bool;

}