<?php

namespace nomit\Web\Url;

use nomit\Dumper\Dumper;
use nomit\Exception\InvalidArgumentException;

class UrlScript extends UrlImmutable
{

    private string $scriptPath;

    private string $basePath;

    public function __construct(string|UrlInterface $url = '/', string $scriptPath = '')
    {
        $this->scriptPath = $scriptPath;

        parent::__construct($url);

        $this->build();
    }

    public function withPath(string $path, string $scriptPath = ''): static
    {
        $dolly = clone $this;
        $dolly->scriptPath = $scriptPath;
        $parent = \Closure::fromCallable([UrlImmutable::class, 'withPath'])->bindTo($dolly);

        return $parent($path);
    }

    public function getScriptPath(): string
    {
        return $this->scriptPath;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getRelativePath(): string
    {
        return substr($this->getPath(), strlen($this->basePath));
    }

    public function getBaseUrl(): string
    {
        return $this->getHostUrl() . $this->basePath;
    }

    public function getRelativeUrl(): string
    {
        return substr($this->getAbsoluteUrl(), strlen($this->getBaseUrl()));
    }

    /**
     * Returns the additional path information.
     */
    public function getPathInfo(): string
    {
        return (string) substr($this->getPath(), strlen($this->scriptPath));
    }

    protected function build(): void
    {
        parent::build();

        $path = $this->getPath();
        $this->scriptPath = $this->scriptPath ?: $path;

        if($this->scriptPath === '') {
            $this->basePath = '/';

            return;
        }

        $pos = strrpos($this->scriptPath, '/');

        if ($pos === false || strncmp($this->scriptPath, $path, $pos + 1)) {
            throw new InvalidArgumentException("ScriptPath '$this->scriptPath' doesn't match path '$path'");
        }

        $this->basePath = substr($this->scriptPath, 0, $pos + 1);
    }

}