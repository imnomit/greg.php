<?php

namespace Application\Resource;

use nomit\DependencyInjection\Container;
use nomit\Dumper\Dumper;
use nomit\Resource\DirectoryResource;
use nomit\Resource\Exception\MismatchedVersionsException;
use nomit\Resource\Manifest\ResourceManifest;
use nomit\Resource\Manifest\ResourceManifestInterface;
use nomit\Resource\Manifest\Store\FileStore;
use nomit\Resource\Resource;
use nomit\Resource\Version\MetadataHashFileVersion;
use nomit\Serialization\SerializerResolverInterface;
use nomit\Web\Response\FileResponse;
use nomit\Web\Response\JsonResponse;
use nomit\Web\Response\ResponseInterface;

class ResourceDumper implements ResourceDumperInterface
{

    public function __construct(
        private Container $container,
        private SerializerResolverInterface $serializer
    )
    {
    }

    public function dumpManifest(string $filename): ResponseInterface
    {
        $manifest = new ResourceManifest(
            new FileStore($this->container->getParameter('resource.manifest.path')),
            $this->container->getParameter('paths.resources'),
            $this->container->getParameter('paths.tmp') . 'cache' . DIRECTORY_SEPARATOR,
            MetadataHashFileVersion::class,
            $this->container->getParameter('resource.routes')['resource'] ?? ResourceManifestInterface::DEFAULT_ROUTE,
            $this->serializer,
            'json'
        );

        $result = $manifest->getResourceManifest();

        return new JsonResponse($result->toArray());
    }

    public function dumpResource(string $filename, string $version): ResponseInterface
    {
        $cachePathName = $this->container->getParameter('paths.tmp') . 'cache' . DIRECTORY_SEPARATOR;
        $originalFilename = $filename;
        $filename = $this->container->getParameter('paths.resources') . $originalFilename;

        $resource = Resource::getResource($filename, new MetadataHashFileVersion($filename, '%s?version=%s'));

        if($version !== ($resourceVersion = $resource->getVersion()->getVersion())) {
            throw new MismatchedVersionsException($version, $resourceVersion);
        }

        if($resource instanceof DirectoryResource) {
            $resource = $resource->amalgamate($cachePathName);
        }

        $resource = $resource->minify($cachePathName);
        $file = $resource->getFile();

        $response = new FileResponse($file);

        $response->setAutoEtag();
        $response->setAutoLastModified();

        return $response;
    }

    private function minifyStylesheet(string $string): string
    {
        $last = '';

        return preg_replace_callback(
            <<<'XX'
				(
					(^
						|'(?:\\.|[^\n'\\])*'
						|"(?:\\.|[^\n"\\])*"
						|([0-9A-Za-z_*#.%:()[\]-]+)
						|.
					)(?:\s|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
				())sx
				XX,
            function ($match) use (&$last) {
                [, $result, $word] = $match;
                if ($last === ';') {
                    $result = $result === '}' ? '}' : ';' . $result;
                    $last = '';
                }

                if ($word !== '') {
                    $result = ($last === 'word' ? ' ' : '') . $result;
                    $last = 'word';
                } elseif ($result === ';') {
                    $last = ';';
                    $result = '';
                } else {
                    $last = '';
                }

                return $result;
            },
            $string . "\n",
        );
    }

    private function minifyScript(string $string): string
    {
        $last = '';

        return preg_replace_callback(
            <<<'XX'
				(
					(?:
						(^|[-+\([{}=,:;!%^&*|?~]|/(?![/*])|return|throw) # context before regexp
						(?:\s|//[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
						(/(?![/*])(?:\\[^\n]|[^[\n/\\]|\[(?:\\[^\n]|[^]])++)+/) # regexp
						|(^
							|'(?:\\.|[^\n'\\])*'
							|"(?:\\.|[^\n"\\])*"
							|([0-9A-Za-z_$]+)
							|([-+]+)
							|.
						)
					)(?:\s|//[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
				())sx
				XX,
            function ($match) use (&$last) {
                [, $context, $regexp, $result, $word, $operator] = $match;
                if ($word !== '') {
                    $result = ($last === 'word' ? ' ' : ($last === 'return' ? ' ' : '')) . $result;
                    $last = ($word === 'return' || $word === 'throw' || $word === 'break' ? 'return' : 'word');
                } elseif ($operator) {
                    $result = ($last === $operator[0] ? ' ' : '') . $result;
                    $last = $operator[0];
                } else {
                    if ($regexp) {
                        $result = $context . ($context === '/' ? ' ' : '') . $regexp;
                    }

                    $last = '';
                }

                return $result;
            },
            $string . "\n",
        );
    }

}