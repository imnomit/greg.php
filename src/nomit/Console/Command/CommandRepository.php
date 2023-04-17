<?php

namespace nomit\Console\Command;

use nomit\Dumper\Dumper;
use Psr\Container\ContainerInterface;
use nomit\Console\Exception\UnresolvableCommandConsoleException;
use nomit\Console\Input\InputInterface;
use nomit\Utility\Bag\BagInterface;
use nomit\Utility\Bag\Bag;

class CommandRepository implements CommandRepositoryInterface
{

    public function __construct(
        private ContainerInterface $container,
        private array $map = []
    )
    {
    }

    public function get(string $name): CommandInterface
    {
        if(!$this->has($name)) {
            throw new UnresolvableCommandConsoleException(sprintf('No command named "%s" exists in the "%s" command repository.', $name, __CLASS__));
        }

        return $this->container->get($this->map[$name]);
    }

    public function has(string $name): bool
    {
        return isset($this->map[$name])
            && $this->container->has($this->map[$name]);
    }

    public function getNames()
    {
        return array_keys($this->map);
    }

}