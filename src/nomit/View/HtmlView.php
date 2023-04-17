<?php

namespace nomit\View;

use nomit\Dumper\Dumper;
use nomit\FileSystem\File\File;
use nomit\Resource\Hash\MetadataResolverHash;
use nomit\Resource\Manifest\ResourceManifest;
use nomit\Resource\Manifest\ResourceManifestInterface;
use nomit\Resource\Manifest\Store\FileStore;
use nomit\Resource\Version\MetadataHashFileVersion;
use nomit\Template\Engine;
use nomit\Utility\Collection\Collection;
use nomit\Utility\Collection\CollectionInterface;
use nomit\View\Resource\ResourceInterface;
use nomit\View\Resource\ScriptResource;
use nomit\View\Resource\StylesheetResource;
use nomit\View\Template\Template;
use nomit\Web\Response\Response;
use nomit\Web\Response\ResponseInterface;
use Psr\Container\ContainerInterface;

class HtmlView extends AbstractView
{

    protected string $contentType = 'text/html';

    private ?string $title = null;

    private iterable $headResources = [
        'favicon' => [],
        'meta' => [],
        'link' => [],
        'script' => []
    ];

    private iterable $bodyResources = [
        'script' => []
    ];

    private ?CollectionInterface $resourceManifest = null;

    public function __construct(
        bool|\Closure|null $debug,
        private ?ContainerInterface $container = null,
        private ?string $templatePath = null,
        private ?string $resourceManifestPath = null,
        private ?string $resourcesPath = null,
        private ?string $cachePath = null,
        ?string $charset = null,
        private $outputBuffer = '',
        array $parameters = []
    )
    {
        if($this->templatePath === null) {
            $reflection = new \ReflectionClass(get_class($this));
            $directory = dirname($reflection->getFileName());

            $this->templatePath = $directory . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'view.html';
        }

        if(!$this->resourceManifestPath && $this->container) {
            $this->resourceManifestPath = $this->container->getParameter('resource.manifest.path');
        }

        if(!$this->resourcesPath && $this->container) {
            $this->resourcesPath = $this->container->getParameter('paths.resources');
        }

        if(!$this->cachePath && $this->container) {
            $this->cachePath = $this->container->getParameter('paths.tmp') . 'cache' . DIRECTORY_SEPARATOR;
        }

        parent::__construct($debug, $charset, $parameters);
    }

    public function getEngine(): Engine
    {
        return $this->container?->has('template.engine') ? $this->container?->get('template.engine') : new Engine();
    }

    public function setTemplatePath(string $path): self
    {
        $this->templatePath = $path;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->set('title', $title);

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->get('title');
    }

    public function addFavicon(ResourceInterface $resource): self
    {
        $this->headResources['favicon'][] = $resource;

        return $this;
    }

    public function addMeta(ResourceInterface $resource): self
    {
        $this->headResources['meta'][] = $resource;

        return $this;
    }

    public function addLink(ResourceInterface $resource): self
    {
        $this->headResources['link'][] = $resource;

        return $this;
    }

    public function addScript(ResourceInterface $resource, int $location = self::LOCATION_BODY): self
    {
        if($location === self::LOCATION_HEAD) {
            $this->headResources['script'][] = $resource;
        } else {
            $this->bodyResources['script'][] = $resource;
        }

        return $this;
    }

    public function getOutputBuffer(): string
    {
        return is_callable($this->outputBuffer) ? ($this->outputBuffer)($this) : $this->outputBuffer;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->loadDefaultStylesheets();
        $this->loadDefaultScripts();
    }

    private function getResourceManifest(): CollectionInterface
    {
        if($this->resourceManifest) {
            return $this->resourceManifest;
        }

        if(!$this->resourceManifestPath
            || !$this->resourcesPath
            || !$this->cachePath
        ) {
            return $this->resourceManifest = new Collection();
        }

        $manifest = new ResourceManifest(
            new FileStore(new File($this->resourceManifestPath)),
            $this->resourcesPath,
            $this->cachePath,
            MetadataHashFileVersion::class,
            $this->container?->getParameter('resource.routes')['resource'] ?? ResourceManifestInterface::DEFAULT_ROUTE,
            $this->container?->get('serializer') ?? null
        );

        $manifest = $manifest->getResourceManifest();
        $manifest = $manifest->toArray();

        return $this->resourceManifest = new Collection($manifest);
    }

    private function loadDefaultStylesheets(): void
    {
        $manifest = $this->getResourceManifest();

        foreach($manifest->get('glittr.stylesheets') ?? [] as $stylesheet) {
            $this->addLink(new StylesheetResource($stylesheet));
        }
    }

    private function loadDefaultScripts(): void
    {
        $manifest = $this->getResourceManifest();

        foreach($manifest->get('glittr.scripts.head') ?? [] as $script) {
            $this->addScript(new ScriptResource($script), self::LOCATION_HEAD);
        }

        foreach($manifest->get('glittr.scripts.body') ?? [] as $script) {
            $this->addScript(new ScriptResource($script), self::LOCATION_BODY);
        }
    }

    protected function prepare(): void
    {
        $headResources = [];

        foreach($this->headResources as $name => $resources) {
            /**
             * @var ResourceInterface $resource
             */
            foreach($resources as $resource) {
                $headResources[] = $resource;
            }
        }

        $bodyResources = [];

        foreach($this->bodyResources as $name => $resources) {
            /**
             * @var ResourceInterface $resource
             */
            foreach($resources as $resource) {
                $bodyResources[] = $resource;
            }
        }

        $data = [
            'title' => $this->getTitle(),
            'dom' => [
                'head' => $headResources,
                'body' => $bodyResources,
            ]
        ];

        foreach($data as $name => $value) {
            $this->set($name, $value);
        }
    }

    protected function build(): ResponseInterface
    {
        $buffer = $this->getOutputBuffer();

        if($buffer !== '') {
            $this->set('buffer', $buffer);
        }

        $response = new Response();

        $response->setContent($this->consolidate());

        return $response;
    }

    protected function consolidate(): string
    {
        $template = new Template($this->getEngine(), $this->templatePath, $this->all());

        return $template->render();
    }

}