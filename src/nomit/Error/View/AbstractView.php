<?php

namespace nomit\Error\View;

use nomit\Error\Extension\ExtensionInterface;
use nomit\Error\Frame\Inspector;
use nomit\Error\Frame\InspectorInterface;
use nomit\Utility\Concern\Stringable;
use nomit\Utility\Repository\Repository;
use nomit\Utility\Repository\RepositoryInterface;
use nomit\Web\Bag\ResponseHeaderBag;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\FileResponse;
use nomit\Web\Response\Response;
use nomit\Web\Response\ResponseInterface;

abstract class AbstractView implements ViewInterface
{

    protected ?string $title = null;

    protected string $contentType = 'text/html';

    protected RepositoryInterface $repository;

    protected RepositoryInterface $headers;

    protected $content;

    protected bool|\Closure|null $debug;

    protected int $errorLimit = \E_ALL;

    private ?RequestInterface $request = null;

    protected array $extensions = [];

    public function __construct(
        bool|\Closure|null $debug = null,
        protected ?string $charset = null,
        array|RepositoryInterface $parameters = []
    )
    {
        $this->debug = is_bool($debug) || $debug instanceof \Closure ? $debug : \Closure::fromCallable($debug);

        if(!$parameters instanceof RepositoryInterface) {
            $this->repository = new Repository($parameters);
        } else {
            $this->repository = $parameters;
        }

        $this->headers = new Repository();
    }

    public function setRequest(RequestInterface $request): ViewInterface
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    public function addExtension(ExtensionInterface $extension): ViewInterface
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function setErrorLimit($limit = \E_ALL): ViewInterface
    {
        $this->errorLimit = $limit;

        return $this;
    }

    public function getErrorLimit(): int
    {
        return $this->errorLimit;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function add(array $data): self
    {
        foreach($data as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    public function set(string $name, mixed $value): self
    {
        $this->repository->set($name, $value);

        return $this;
    }

    public function has(string $name): bool
    {
        return $this->repository->has($name);
    }

    public function get(string $name): mixed
    {
        return $this->repository->get($name);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = new Repository();

        return $this->addHeaders($headers);
    }

    public function addHeaders(array $headers): self
    {
        foreach($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers->set($name, $value);

        return $this;
    }

    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    public function getHeader(string $name): string
    {
        return $this->headers->get($name);
    }

    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    protected function loadHeaders(ResponseInterface $response): void
    {
        $response->headers->add($this->getHeaders());
    }

    public function setContent(string|null|callable $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function hasContent(): bool
    {
        return $this->content !== null;
    }

    public function getContent(mixed $content = null): string|null
    {
        if(is_callable($this->content)) {
            return $this->getContent(($this->content)($this));
        }

        return (string) ($content ?? $this->content);
    }

    protected function configure(InspectorInterface $inspector): void
    {
        $debug = \is_bool($this->debug) ? $this->debug : ($this->debug)($inspector);
        $exceptionMessage = $this->escape($inspector->getMessage());
        $statusText = $this->escape($inspector->getStatusText());
        $statusCode = $this->escape($inspector->getStatusCode());

        $inspector->setDebug($debug);

        if($debug) {
            $this->add([
                'exceptions' => $inspector->toArray(),
                'message' => $exceptionMessage,
                'status.text' => $statusText,
                'status.code' => $statusCode,
                'request' => $this->getRequest()->toArray()
            ]);

            if($this instanceof HtmlView) {
                $this->set('content', $this->getOutputBuffer());
            }
        } else {
            $this->add([
                'status.text' => $statusText,
                'status.code' => $statusCode,
                'exceptions' => $inspector->toArray()
            ]);
        }

        if ($debug) {
            $this->addHeaders([
                'X-Debug-Exception' => rawurlencode($inspector->getMessage()),
                'X-Debug-Exception-File' => rawurlencode($inspector->getFile()) . ':' . $inspector->getLine()
            ]);
        }

        $this->setTitle($exceptionMessage . ' - ' . 'Something\'s Wrong Here');
    }

    protected function prepare(InspectorInterface $inspector): void
    {
        $extensions = $this->getExtensions();

        $this->set('extensions', $extensions);

        foreach($extensions as $extension) {
            $extension->render($this, $inspector);
        }
    }

    abstract protected function build(InspectorInterface $inspector): ResponseInterface;

    public function render(\Throwable $exception): \nomit\Web\Response\ResponseInterface
    {
        $inspector = Inspector::create($exception);

        $this->configure($inspector);

        $this->prepare($inspector);

        $response = $this->build($inspector);

        return $this->dispatch($response);
    }

    protected function dispatch(ResponseInterface|string|Stringable|callable $response): ResponseInterface
    {
        if(!$response instanceof ResponseInterface) {
            $this->setContent($response);

            $response = new Response();
        }

        $this->process($response);

        return $response;
    }

    protected function process(ResponseInterface $response): void
    {
        if(!$response instanceof FileResponse
            && !$response->hasContent()
            && $this->hasContent()
        ) {
            $response->setContent($this->getContent());
        }

        $this->loadHeaders($response);

        $contentType = method_exists($this, 'getContentType')
            ? $this->getContentType()
            : null;

        if($contentType) {
            $response->headers->set('Content-Type', $contentType);
        }

        if($response instanceof FileResponse) {
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $response->getFile()->getFileName()
            );
        }
    }

    protected function escape(string $string): string
    {
        return htmlspecialchars($string, \ENT_COMPAT | \ENT_SUBSTITUTE, $this->charset);
    }

}