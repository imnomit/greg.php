<?php

namespace nomit\Security\Authentication\DependencyInjection\Factory;

use nomit\DependencyInjection\ContainerBuilder;

interface SecurityFactoryInterface
{

    public function getPosition(): string;

    public function getKey(): string;

}