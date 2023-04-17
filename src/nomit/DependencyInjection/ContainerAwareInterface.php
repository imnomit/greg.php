<?php

namespace nomit\DependencyInjection;

use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{

    public function setContainer(ContainerInterface $container): self;

    public function getContainer(): ?ContainerInterface;

}