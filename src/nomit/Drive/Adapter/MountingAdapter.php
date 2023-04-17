<?php

namespace nomit\Drive\Adapter;

use nomit\Drive\Exception\Adapter\AdapterException;
use nomit\Drive\Exception\InvalidArgumentException;
use nomit\Drive\Pathname\PathnameInterface;
use nomit\Drive\Utility\FileSystemUtility;
use nomit\Utility\Arrays;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Stringable;

class MountingAdapter extends AbstractDelegatingAdapter implements MountingAdapterInterface
{

    protected array $mounts = [];

    public function mount(string|PathnameInterface|Stringable $pathname, AdapterInterface $adapter): MountingAdapterInterface
    {
        $pathname = $this->normalizePathname($pathname);

        if($this->isMounted($pathname)) {
            throw new InvalidArgumentException(sprintf('The supplied pathname, "%s", could not be mounted, as it has already been previously mounted.', $pathname));
        }

        Arrays::set($this->mounts, $pathname, $adapter);

        return $this;
    }

    public function isMounted(string|PathnameInterface|Stringable $pathname): bool
    {
        return Arrays::has($this->mounts, ConcernUtility::toString($pathname));
    }

    public function unmount(string|PathnameInterface|Stringable $pathname): void
    {
        $pathname = $this->normalizePathname($pathname);

        if(!$this->isMounted($pathname)) {
            throw new AdapterException(
                $this,
                sprintf('The supplied pathname, "%s", does not reference an existing mount, and so cannot be unmounted.', $pathname)
            );
        }

        Arrays::remove($this->mounts, $pathname);
    }

    private function normalizePathname(string|PathnameInterface|Stringable $pathname): string
    {
        $pathname = ConcernUtility::toString($pathname);
        $pathname = FileSystemUtility::normalizePathname($pathname);

        if(empty($pathname)) {
            throw new InvalidArgumentException('The supplied mount pathname is empty.');
        }

        return $pathname;
    }

    protected function delegate(PathnameInterface $pathname = null): AbstractDelegatingAdapter
    {
        if($pathname !== null) {
            $path = $pathname->getPathname();

            do {
                if($this->isMounted($path)) {
                    return $this->mounts[$path];
                }
            } while('/' !== $path = FileSystemUtility::getDirectory($path));

            throw new AdapterException(
                $this,
                sprintf('No mount could be found for the supplied pathname, "%s".', $pathname)
            );
        }

        return $this;
    }

    public function resolveLocalPathname(PathnameInterface $pathname, AdapterInterface &$localAdapter, string &$localAdapterPathname): AdapterInterface
    {
        $path = $pathname->getPathname();

        do {
            if($this->isMounted($path)) {
                $localAdapter = $this->mounts[$path];
                $localAdapterPathname = substr($pathname->getPathname(), strlen($path));

                return $this;
            }
        } while('/' !== $path = FileSystemUtility::getDirectory($path));

        throw new AdapterException(
            $this,
            sprintf('No mount could be found for the supplied pathname, "%s".', $pathname)
        );
    }

}