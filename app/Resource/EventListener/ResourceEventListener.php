<?php

namespace Application\Resource\EventListener;

use Application\EventListener\AbstractRoutingEventListener;
use Application\Resource\ResourceDumperInterface;
use nomit\Dumper\Dumper;
use nomit\EventDispatcher\AbstractEventSubscriber;
use nomit\Exception\ExceptionInterface;
use nomit\Exception\InvalidArgumentException;
use nomit\Exception\LogicException;
use nomit\Kernel\Event\KernelEvents;
use nomit\Kernel\Event\RequestEvent;
use nomit\Routing\Exception\PageNotFoundException;
use nomit\Routing\RouteCollector;
use nomit\Routing\RouteInterface;
use nomit\Routing\Router;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class ResourceEventListener extends AbstractRoutingEventListener
{

    public function __construct(
        ContainerInterface $container,
        private ResourceDumperInterface $dumper,
        array $options = [
            'routes' => [
                'resource' => '/resource/<path .+>',
                'manifest' => '/resources/manifest/<path .+>'
            ]
        ],
        ?LoggerInterface $logger = null,
    )
    {
        parent::__construct($container, $options, $logger);
    }

    protected function createRouter(array $options): Router
    {
        $router = parent::createRouter($options);

        $router->addPatterns([
            '@path' => '.+',
        ]);

        return $router;
    }

    public function handle(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $route = $request->attributes->get($this->getAttributeName());

        match($route->getName()) {
            'resource' => $response = $this->getResource($request),
            'manifest' => $response = $this->getResourceManifest($request),
            default => throw new InvalidArgumentException(sprintf('The matched route, with a presenter named "%s", is not supported by the "%s" dispatcher.', $route->getName(), self::class))
        };

        if(!$response instanceof ResponseInterface) {
            throw new LogicException(sprintf('A "%s"-typed object is expected to be returned following dispatching by the "%s". Instead, a "%s"-typed response value was returned.', ResponseInterface::class, self::class, get_debug_type($response)));
        }

        return $response;
    }

    public function getResource(RequestInterface $request): ResponseInterface
    {
        $version = $request->query->get('version');
        $route = $request->attributes->get($this->getAttributeName());
        $pathname = $route->getAttributes()['path'] ?? null;

        if(!$pathname) {
            throw new LogicException(sprintf('The expected "path" parameter does not exist in the matched route data.'));
        }

        return $this->dumper->dumpResource($pathname, $version);
    }

    public function getResourceManifest(RequestInterface $request): ResponseInterface
    {
        $route = $request->attributes->get($this->getAttributeName());
        $pathname = $route->getAttributes()['path'] ?? null;

        if(!$pathname) {
            throw new LogicException(sprintf('The expected "path" parameter does not exist in the matched route data.'));
        }

        return $this->dumper->dumpManifest($pathname);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public static function getDispatcherName(): ?string
    {
        return 'event_dispatcher';
    }

}