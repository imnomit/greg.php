<?php

namespace nomit\Kernel\Component;

use nomit\DependencyInjection\Exception\MissingServiceException;
use nomit\DependencyInjection\Exception\ServiceCreationException;
use nomit\Dumper\Dumper;
use nomit\Exception\RuntimeException;
use nomit\Kernel\Exception\InvalidControllerException;
use Psr\Container\ContainerInterface;

final class ControllerFactoryCallback
{

    public function __construct(
        private ContainerInterface $container,
        private ?string $touchToRefresh
    )
    {
    }

    public function __invoke(string $class): ControllerInterface
    {
        $services = $this->container->findByType($class);

        if (count($services) > 1) {
            $exact = array_keys(array_map([$this->container, 'getServiceType'], $services), $class, true);

            if (count($exact) === 1) {
                return $this->container->createService($services[$exact[0]]);
            }

            throw new InvalidControllerException("Multiple services of type $class found: " . implode(', ', $services) . '.');

        } elseif (!$services) {
            if ($this->touchToRefresh) {
                touch($this->touchToRefresh);
            }

            try {
                $controller = $this->container->createInstance($class);

                $this->container->callInjects($controller);
            } catch (MissingServiceException | ServiceCreationException $e) {
                if ($this->touchToRefresh && class_exists($class)) {
                    throw new RuntimeException("Refresh your browser. New presenter $class was found.", 0, $e);
                }

                throw $e;
            }

            return $controller;
        }

        return $this->container->createService($services[0]);
    }

}