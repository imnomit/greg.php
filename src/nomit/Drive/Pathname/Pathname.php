<?php

namespace nomit\Drive\Pathname;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Utility\FileSystemUtility;

final class Pathname implements PathnameInterface
{

    private string $pathname;

    private ?string $localAdapter = null;

    private ?string $localAdapterPathname = null;

    public function __construct(
        private AdapterInterface $adapter,
        string $pathname
    )
    {
        $this->pathname = FileSystemUtility::normalizePathname($pathname);
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getBasename(): string
    {
        return basename($this->getPathname());
    }

    public function getLocalAdapter(): AdapterInterface
    {
        if($this->localAdapter === null) {
            $this->adapter->resolveLocalAdapter(
                $this,
                $this->localAdapter,
                $this->localAdapterPathname
            );
        }

        return $this->localAdapter;
    }

    public function getLocalPathname(): string
    {
        if($this->localAdapterPathname === null) {
            $this->adapter->resolveLocalAdapter(
                $this,
                $this->localAdapter,
                $this->localAdapterPathname
            );
        }

        return $this->localAdapterPathname;
    }

    public function getParent(): PathnameInterface
    {
        return new self(
            $this->adapter,
            FileSystemUtility::getDirectory($this->getPathname())
        );
    }

    public function getChild(string|PathnameInterface $basename): PathnameInterface
    {
        if($basename instanceof PathnameInterface) {
            $basename = $basename->getBasename();
        }

        return new self(
            $this->adapter,
            $this->getPathname() . DIRECTORY_SEPARATOR . $basename
        );
    }

    public function toString(): string
    {
        return $this->getPathname();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}