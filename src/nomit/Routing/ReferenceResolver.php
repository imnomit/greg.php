<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2018, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router
 */

namespace nomit\Routing;

/**
 * Import classes
 */

use nomit\DependencyInjection\Exception\MissingServiceException;
use nomit\Dumper\Dumper;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Kernel\Component\ControllerFactoryInterface;
use nomit\Kernel\Component\ControllerInterface;
use nomit\Kernel\Event\ControllerEvent;
use nomit\Kernel\Event\KernelEvents;
use nomit\Kernel\Exception\InvalidControllerException;
use nomit\Kernel\Utilities\KernelUtilities;
use nomit\Routing\RequestHandler\ControllerRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use nomit\Routing\Exception\UnresolvableReferenceException;
use nomit\Routing\Middleware\CallableMiddleware;
use nomit\Routing\RequestHandler\CallableRequestHandler;
use Closure;

/**
 * Import functions
 */
use function is_array;
use function is_callable;
use function is_string;
use function is_subclass_of;
use function method_exists;
use function sprintf;

/**
 * ReferenceResolver
 *
 * @since 2.10.0
 */
class ReferenceResolver implements ReferenceResolverInterface
{

    public function __construct(
        private ?ContainerInterface $container = null,
        private ?ControllerFactoryInterface $controllerFactory = null,
        private ?EventDispatcherInterface $dispatcher = null
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer() : ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(?ContainerInterface $container) : void
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function toRequestHandler($reference) : RequestHandlerInterface
    {
        if(is_string($reference)) {
            if(str_contains($reference, ':')) {
                [$controllerName, $action] = KernelUtilities::splitName($reference);
            } else {
                $controllerName = $reference;
            }

            try {
                $controller = $this->controllerFactory->createController($controllerName);
            } catch(InvalidControllerException $exception) {
                goto resolve;
            }

            $controller->setName($controllerName);

            if(isset($action)) {
                $controller->setAction($action);
            }

            if($this->dispatcher) {
                $event = new ControllerEvent($controller);

                $this->dispatcher->dispatch($event, KernelEvents::CONTROLLER);

                $controller = $event->getController();
            }

            return new ControllerRequestHandler($controller);
        }

        if ($reference instanceof RequestHandlerInterface) {
            return $reference;
        }

        if ($reference instanceof Closure) {
            return new CallableRequestHandler($reference);
        }

        resolve:
        list($class, $method) = $this->normalizeReference($reference);

        if (isset($class) && isset($method) && method_exists($class, $method)) {
            return new CallableRequestHandler([$this->resolveClass($class), $method]);
        }

        if (!isset($method) && isset($class) && is_subclass_of($class, RequestHandlerInterface::class)) {
            return $this->resolveClass($class);
        }

        throw new UnresolvableReferenceException(sprintf(
            'Unable to resolve the "%s" reference to a request handler.',
            $this->stringifyReference($reference)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function toMiddleware($reference) : MiddlewareInterface
    {
        if ($reference instanceof MiddlewareInterface) {
            return $reference;
        }

        if ($reference instanceof Closure) {
            return new CallableMiddleware($reference);
        }

        list($class, $method) = $this->normalizeReference($reference);

        if (isset($class) && isset($method) && method_exists($class, $method)) {
            return new CallableMiddleware([$this->resolveClass($class), $method]);
        }

        if (!isset($method) && isset($class) && is_subclass_of($class, MiddlewareInterface::class)) {
            return $this->resolveClass($class);
        }

        throw new UnresolvableReferenceException(sprintf(
            'Unable to resolve the "%s" reference to a middleware.',
            $this->stringifyReference($reference)
        ));
    }

    /**
     * Normalizes the given reference
     *
     * @param mixed $reference
     *
     * @return array{0: ?class-string, 1: ?string}
     */
    private function normalizeReference($reference) : array
    {
        if (is_array($reference) && is_callable($reference, true)) {
            /** @var array{0: class-string, 1: string} $reference */

            return $reference;
        }

        if (is_string($reference)) {
            /** @var class-string $reference */

            return [$reference, null];
        }

        return [null, null];
    }

    /**
     * Stringifies the given reference
     *
     * @param mixed $reference
     *
     * @return string
     */
    private function stringifyReference($reference) : string
    {
        if (is_array($reference) && is_callable($reference, true)) {
            return $reference[0] . '@' . $reference[1];
        }

        if (is_string($reference)) {
            return $reference;
        }

        return '';
    }

    /**
     * Resolves the given class
     *
     * @param class-string<T> $class
     * @template T
     */
    private function resolveClass(string $class)
    {
        if ($this->container) {
            if($this->container->has($class)) {
                return $this->container->get($class);
            }

            if($object = $this->container->getByType($class)) {
                return $object;
            }
        }

        return new $class;
    }
}
