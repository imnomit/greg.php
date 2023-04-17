<?php

namespace nomit\Toasting\Response;

use Application\Controller\Index\IndexController;
use nomit\Dumper\Dumper;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Exception\InvalidArgumentException;
use nomit\Toasting\Event\LoadEnvelopesEvent;
use nomit\Toasting\Event\ResponseEvent;
use nomit\Toasting\Event\ViewEvent;
use nomit\Toasting\Response\View\ViewInterface;
use nomit\Toasting\Storage\StorageManagerInterface;
use nomit\Web\Request\RequestInterface;

final class Responder implements ResponderInterface
{

    private array $views = [];

    public function __construct(
        private StorageManagerInterface $storageManager,
        private ?EventDispatcherInterface $dispatcher = null,
        array $views = []
    )
    {
        foreach($views as $format => $view) {
            $this->addView($format, $view);
        }
    }

    public function addView(string $format, ViewInterface $view): self
    {
        $this->views[$format] = $view;

        return $this;
    }

    public function load(array $criteria = [], array $context = []): mixed
    {
        $envelopes = $this->storageManager->filter($criteria);

        $this->storageManager->remove($envelopes);

        if(count($envelopes) < 1) {
            return null;
        }

        $event = new ViewEvent($envelopes, $context);

        $this->dispatcher?->dispatch($event);

        return $this->createResponse($event->getEnvelopes(), $context);
    }

    public function render(array $criteria = [], string $view = 'html', array $context = []): mixed
    {
        $response = $this->load($criteria, $context);

        if(!$response) {
            return null;
        }

        $result = $this->createView($view)->render($response);

        $event = new ResponseEvent($response, $result);

        $this->dispatcher?->dispatch($event);

        return $event->getResult();
    }

    private function createView(string $format): ViewInterface
    {
        if(!isset($this->views[$format])) {
            throw new InvalidArgumentException(sprintf('The toast view "%s" is not supported. The supported views are: "%s".', $format, implode(', ', array_keys($this->views))));
        }

        $view = $this->views[$format];

        return \is_callable($view) ? $view() : $view;
    }

    private function createResponse(array $envelopes, array $context): ResponseInterface
    {
        return new Response($envelopes, $context);
    }

}