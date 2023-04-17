<?php

namespace nomit\Kernel\Component;

use nomit\Exception\InvalidStateException;
use nomit\Kernel\Exception\InvalidControllerException;
use nomit\Utility\Object\SmartObjectTrait;
use nomit\Utility\String\Strings;
use Psr\Container\ContainerInterface;

class ControllerFactory implements ControllerFactoryInterface
{

    use SmartObjectTrait;

    /** @var array[] of module => splited mask */
    private array $mapping = [
        '*' => ['', '*Module\\', '*Controller'],
        'nomit' => ['nomitModule\\', '*\\', '*Controller'],
    ];

    private array $cache = [];

    /** @var callable */
    private $factory;

    public function __construct(
        private ?ContainerInterface $container = null,
        ?callable $factory = null
    )
    {
        $this->factory = $factory ?: fn(string $class): ControllerInterface => new $class;
    }

    public function createController(string $name): ControllerInterface
    {
        return ($this->factory)($this->getControllerClass($name));
    }

    public function getControllerClass(string &$name): string
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (!Strings::match($name, '#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*$#D')) {
            throw new InvalidControllerException("Controller name must be alphanumeric string, '$name' is invalid.");
        }

        $class = $this->formatControllerClass($name);

        if (!class_exists($class)) {
            $testClass = sprintf('Application\\Controller\\%s\\%s', $name, $class);

            if(class_exists($testClass)) {
                $class = $testClass;
            } else {
                throw new InvalidControllerException("Cannot load controller '$name', class '$class' was not found.");
            }
        }

        $reflection = new \ReflectionClass($class);
        $class = $reflection->getName();

        if (!$reflection->implementsInterface(ControllerInterface::class)) {
            throw new InvalidControllerException(sprintf('Cannot load parameter "%s", because class "%s" is not "%s" implementor.', $name, $class, ControllerInterface::class));
        } else if ($reflection->isAbstract()) {
            throw new InvalidControllerException("Cannot load controller '$name', class '$class' is abstract.");
        }

        return $this->cache[$name] = $class;
    }

    public function setMapping(array $mapping): self
    {
        foreach ($mapping as $module => $mask) {
            if (is_string($mask)) {
                if (!preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)$#D', $mask, $m)) {
                    throw new InvalidStateException("Invalid mapping mask '$mask'.");
                }

                $this->mapping[$module] = [$m[1], $m[2] ?: '*Module\\', $m[3]];
            } elseif (is_array($mask) && count($mask) === 3) {
                $this->mapping[$module] = [$mask[0] ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
            } else {
                throw new InvalidStateException("Invalid mapping mask for module $module.");
            }
        }

        return $this;
    }

    public function formatControllerClass(string $controller): string
    {
        $parts = explode(':', $controller);
        $mapping = isset($parts[1], $this->mapping[$parts[0]])
            ? $this->mapping[array_shift($parts)]
            : $this->mapping['*'];

        while ($part = array_shift($parts)) {
            $mapping[0] .= str_replace('*', $part, $mapping[$parts ? 1 : 2]);
        }

        return $mapping[0];
    }

}