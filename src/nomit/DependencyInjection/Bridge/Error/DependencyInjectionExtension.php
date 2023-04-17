<?php

namespace nomit\DependencyInjection\Bridge\Error;

use nomit\DependencyInjection\Container;
use nomit\Error\Extension\Extension;
use nomit\Error\Frame\InspectorInterface;
use nomit\Error\View\ViewInterface;

final class DependencyInjectionExtension extends Extension
{

    public function __construct(
        private Container $container
    )
    {
    }

    public function extract(): array
    {
        $container = $this->container;

        $rc = new \ReflectionClass($container);
        $tags = [];
        $types = [];

        foreach ($rc->getMethods() as $method) {
            if (preg_match('#^createService(.+)#', $method->name, $m) && $method->getReturnType()) {
                $types[lcfirst(str_replace('__', '.', $m[1]))] = (string) $method->getReturnType();
            }
        }

        $types = $this->getContainerProperty('types') + $types;

        ksort($types, SORT_NATURAL);

        foreach ($this->getContainerProperty('tags') as $tag => $tmp) {
            foreach ($tmp as $service => $val) {
                $tags[$service][$tag] = $val;
            }
        }

        $file = $rc->getFileName();
        $instances = $this->getContainerProperty('instances');
        $wiring = $this->getContainerProperty('wiring');

        return [
            'container' => $container,
            'parameters' => $container->getParameters(),
            'tags' => $tags,
            'types' => $types,
            'file' => $file,
            'instances' => $instances,
            'wiring' => $wiring
        ];
    }

    private function getContainerProperty(string $name)
    {
        $prop = (new \ReflectionClass(Container::class))->getProperty($name);
        $prop->setAccessible(true);

        return $prop->getValue($this->container);
    }

    private function getTemplatePathname(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'container.latte';
    }

    public function getName(): string
    {
        return 'dependency_injection';
    }

    public function render(ViewInterface $view, InspectorInterface $inspector): void
    {
        $view->set('di', $this->extract());
    }

}