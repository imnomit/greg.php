<?php

namespace nomit\Kernel;

use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Exception\InvalidStateException;
use nomit\Kernel\Event\ExceptionEvent;
use nomit\Kernel\Event\FinishRequestEvent;
use nomit\Kernel\Event\KernelEvents;
use nomit\Kernel\Event\RequestEvent;
use nomit\Kernel\Event\ResponseEvent;
use nomit\Kernel\Event\ViewEvent;
use nomit\Kernel\Exception\BadRequestException;
use nomit\Kernel\Exception\TooManyRequestsApplicationException;
use nomit\Kernel\Exception\WebExceptionInterface;
use nomit\Routing\Exception\ExceptionInterface as RoutingExceptionInterface;
use nomit\Routing\Router;
use nomit\Utility\Object\SmartObjectTrait;
use nomit\Web\Request\RequestStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Kernel implements KernelInterface
{

    use SmartObjectTrait;

    private bool $debug = false;

    public int $maximumLoops = 20;

    public bool $catchExceptions = true;

    private RequestInterface $request;

    public function __construct(
        private Router $router,
        private RequestStack $requests,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function setDebug(bool $debug = true): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request ?? $this->request = $this->requests->getCurrentRequest();
    }

    public function run(RequestInterface $request = null): void
    {
        $request = $request ?? $this->getRequest();

        if(!$request) {
            throw new InvalidStateException('No current request object could be retrieved from the kernel request stack.');
        }

        $this->request = $request;

        $response = $this->handle($request);

        $response->send();
    }

    public function handle(RequestInterface $request, int $type = self::MAIN_REQUEST): ResponseInterface
    {
        ob_start();

        $request->headers->set('X-Php-Ob-Level', (string) ob_get_level());

        try {
            return $this->process($request, $type);
        } catch(TooManyRequestsApplicationException $exception) {
            throw $exception;
        } catch(\Throwable $exception) {
            if(!$this->catchExceptions) {
                $this->finishRequest($request, $type);

                throw $exception;
            }

            return $this->handleException($exception, $request, self::MAIN_REQUEST);
        }
    }

    private function process(RequestInterface $request, int $type = self::MAIN_REQUEST): ResponseInterface
    {
        if($this->requests->count() > $this->maximumLoops) {
            throw new TooManyRequestsApplicationException('Too many request loops have occurred in the kernel life cycle.');
        }

        $this->requests->push($request);

        $requestEvent = new RequestEvent($this, $request, $type);

        $this->dispatcher->dispatch($requestEvent, KernelEvents::REQUEST);

        if($requestEvent->hasResponse()) {
            return $this->filterResponse($requestEvent->getResponse(), $request, $type);
        }

        return $this->route($request, $type);
    }

    private function route(RequestInterface $request, int $type): ResponseInterface
    {
        try {
            $response = $this->router->handle($request);
        } catch(RoutingExceptionInterface $exception) {
            throw $this->requests->count() > 1
                ? $exception
                : new BadRequestException($exception->getMessage(), 0, $exception);
        }

        if(!$response instanceof ResponseInterface) {
            $viewEvent = new ViewEvent($this, $request, $type, $response);

            $this->dispatcher->dispatch($viewEvent, KernelEvents::VIEW);

            if($viewEvent->hasResponse()) {
                $response = $viewEvent->getResult();
            } else {
                throw new BadRequestException(sprintf('The controller matched to the handled request did not return the expected "%s"-typed object.', ResponseInterface::class));
            }
        }

        return $this->filterResponse($response, $request, $type);
    }

    private function filterResponse(
        ResponseInterface $response,
        RequestInterface $request,
        int $type
    ): ResponseInterface
    {
        $event = new ResponseEvent($this, $request, $type, $response);

        $this->dispatcher->dispatch($event, KernelEvents::RESPONSE);

        $this->finishRequest($request, $type);

        return $event->getResponse();
    }

    private function finishRequest(
        RequestInterface $request,
        int $type
    ): void
    {
        $this->dispatcher->dispatch(new FinishRequestEvent($this, $request, $type), KernelEvents::FINISH_REQUEST);

        $this->requests->pop();
    }

    private function handleException(
        \Throwable $exception,
        RequestInterface $request,
        int $type
    ): ResponseInterface
    {
        $event = new ExceptionEvent($this, $request, $type, $exception);

        $this->dispatcher->dispatch($event, KernelEvents::EXCEPTION);

        $exception = $event->getThrowable();

        if (!$event->hasResponse()) {
            $this->finishRequest($request, $type);

            throw $exception;
        }

        $response = $event->getResponse();

        if ($response
            && !$event->isAllowingCustomResponseCode()
            && !$response->isClientError()
            && !$response->isServerError()
            && !$response->isRedirect()
        ) {
            if ($exception instanceof WebExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());

                $response->headers->add($exception->getHeaders());
            } else {
                $response->setStatusCode($exception instanceof BadRequestException ? ($exception->getStatusCode() ?: 404) : 500);
            }
        }

        try {
            return $this->filterResponse($response, $request, $type);
        } catch(\Throwable $exception) {
            return $response;
        }
    }


    private function makeString($variable): string
    {
        if (\is_object($variable)) {
            return sprintf('an object of type %s', \get_class($variable));
        }

        if (\is_array($variable)) {
            $a = [];
            foreach ($variable as $k => $v) {
                $a[] = sprintf('%s => ...', $k);
            }

            return sprintf('an array ([%s])', mb_substr(implode(', ', $a), 0, 255));
        }

        if (\is_resource($variable)) {
            return sprintf('a resource (%s)', get_resource_type($variable));
        }

        if (null === $variable) {
            return 'null';
        }

        if (false === $variable) {
            return 'a boolean value (false)';
        }

        if (true === $variable) {
            return 'a boolean value (true)';
        }

        if (\is_string($variable)) {
            return sprintf('a string ("%s%s")', mb_substr($variable, 0, 255), mb_strlen($variable) > 255 ? '...' : '');
        }

        if (is_numeric($variable)) {
            return sprintf('a number (%s)', (string) $variable);
        }

        return (string) $variable;
    }

}