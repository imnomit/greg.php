<?php

namespace nomit\Drive;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Adapter\PrimaryAdapter;
use nomit\Drive\Pathname\Pathname;
use nomit\Drive\Plugin\PluginManagerInterface;
use nomit\Drive\Plugin\FileSystemPluginInterface;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Resource\Directory\Directory;
use nomit\Drive\Resource\Directory\DirectoryInterface;
use nomit\Drive\Resource\File\File;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Stream\StreamManager;
use nomit\Drive\Utility\FileSystemUtility;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Utility\Concern\Stringable;

class FileSystem implements FileSystemInterface
{

    protected PrimaryAdapter $adapter;

    protected ?EventDispatcherInterface $dispatcher = null;

    protected ?PluginManagerInterface $pluginManager = null;

    protected ?string $streamHost = null;

    protected ?string $streamScheme = null;

    public function __construct(
        AdapterInterface $adapter
    )
    {
        $this->adapter = new PrimaryAdapter($this);
        $this->adapter->setDelegate($adapter);
    }

    public function __destruct()
    {
        if($this->isStreamingEnabled()) {
            $this->disableStreaming();
        }
    }

    public function getPrimaryAdapter(): PrimaryAdapter
    {
        return $this->adapter;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null): FileSystemInterface
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function setPluginManager(PluginManagerInterface $manager = null): FileSystemInterface
    {
        $this->pluginManager = $manager;

        return $this;
    }

    public function getPluginManager(): ?PluginManagerInterface
    {
        return $this->pluginManager;
    }

    public function enableStreaming(string $host, string $scheme = null): FileSystemInterface
    {
        if($this->isStreamingEnabled()) {
            $this->disableStreaming();
        }

        $this->streamHost = $host;
        $this->streamScheme = $scheme ?: 'nomitfs';

        StreamManager::register($this, $host, $scheme);

        return $this;
    }

    public function disableStreaming(): void
    {
        if($this->isStreamingEnabled()) {
            StreamManager::unregister($this->streamHost, $this->streamScheme);

            $this->streamHost = null;
            $this->streamScheme = null;
        }
    }

    public function isStreamingEnabled(): bool
    {
        return $this->streamHost !== null;
    }

    public function getStreamHost(): ?string
    {
        return $this->streamHost;
    }

    public function getStreamScheme(): ?string
    {
        return $this->streamScheme;
    }

    public function getStreamPrefix(): ?string
    {
        return sprintf('%s://%s', $this->streamScheme, $this->streamHost);
    }

    public function hasPlugin(string|Stringable $name): bool
    {
        return $this->pluginManager
            && $this->pluginManager->has($name)
            && $this->pluginManager->get($name)->providesFileSystem($this);
    }

    public function getPlugin(string|Stringable $name): ?FileSystemPluginInterface
    {
        if($this->pluginManager
            && $this->pluginManager->has($name)
        ) {
            $plugin = $this->pluginManager->get($name);

            if($plugin->providesFileSystem($this)) {
                return $plugin->fromFileSystem($this);
            }
        }

        return null;
    }

    public function getFileRoot(): FileInterface
    {
        return $this->getFile();
    }

    public function getFile(string|PathnameInterface|Stringable $pathname = null): FileInterface
    {
        if($pathname instanceof PathnameInterface
            && $pathname->getAdapter() === $this->adapter
        ) {
            return new File($pathname);
        }

        $pathname = implode('/', FileSystemUtility::getPathnameComponents($pathname));

        $pathname !== '' && $pathname = '/' . $pathname;

        return new File(
            new Pathname($this->adapter, $pathname)
        );
    }

    public function getDirectory(string|PathnameInterface|Stringable $pathname = null): DirectoryInterface
    {
        if($pathname instanceof PathnameInterface
            && $pathname->getAdapter() === $this->adapter
        ) {
            return new Directory($pathname);
        }

        $pathname = implode('/', FileSystemUtility::getPathnameComponents($pathname));

        $pathname !== '' && $pathname = '/' . $pathname;

        return new Directory(
            new Pathname($this->adapter, $pathname)
        );
    }

}