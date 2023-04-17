<?php

namespace nomit\Drive\Adapter;

use nomit\Drive\Adapter\AbstractDelegatingAdapter;
use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Exception\BadMethodCallException;
use nomit\Drive\FileSystemInterface;
use nomit\Drive\Pathname\PathnameInterface;

class PrimaryAdapter extends AbstractDelegatingAdapter
{

    protected ?AdapterInterface $delegateAdapter = null;

    public function __construct(
        FileSystemInterface $fileSystem
    )
    {
        $this->fileSystem = $fileSystem;
    }

    public function setFileSystem(?FileSystemInterface $fileSystem): AdapterInterface
    {
        throw new BadMethodCallException(sprintf('The "$fileSystem" property of the "%s" adapter cannot be overwritten.', get_class($this)));
    }

    public function getPrimaryAdapter(): AdapterInterface
    {
        return $this;
    }

    public function setParentAdapter(AdapterInterface $adapter = null): AdapterInterface
    {
        throw new BadMethodCallException(sprintf('The parent adapter of the "%s" primary adapter cannot be overwritten.', get_class($this)));
    }

    public function getParentAdapter(): self|null
    {
        return $this;
    }

    public function setDelegate(AdapterInterface $delegate): self
    {
        if($this->delegateAdapter !== null) {
            $this->delegateAdapter->setParentAdapter(null);
            $this->delegateAdapter->setFileSystem(null);
        }

        $this->delegateAdapter = $delegate;
        $this->delegateAdapter->setFileSystem($this->fileSystem);
        $this->delegateAdapter->setParentAdapter($this);

        return $this;
    }

    public function getDelegate(): AdapterInterface
    {
        return $this->delegateAdapter;
    }

    protected function delegate(PathnameInterface $pathname = null): \nomit\Drive\Adapter\AbstractDelegatingAdapter
    {
        return $this->delegateAdapter;
    }

}