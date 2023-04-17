<?php

namespace nomit\Notification\Stamp;

final class RouteStamp extends AbstractStamp
{

    public function __construct(
        private array $route
    )
    {
    }

    public function getName(): string
    {
        return 'route';
    }

    public function getRoute(): array
    {
        return $this->route;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'route' => $this->getRoute()
        ];
    }

}