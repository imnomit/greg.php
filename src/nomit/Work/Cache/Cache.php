<?php

namespace nomit\Work\Cache;

use nomit\Cache\StorageInterface;

class Cache implements CacheInterface
{

    protected \nomit\Cache\Cache $cache;

    protected string $prefix;

    public function __construct(StorageInterface $cacheStorage, string $prefix = '_casper.process.cache')
    {
        $this->cache = new \nomit\Cache\Cache($cacheStorage);
        $this->prefix = $prefix;
    }

    protected function getKey(string $key): string
    {
        return $this->prefix . '.' . $key;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->load($this->getKey($key));
    }

    public function set(string $key, mixed $value): CacheInterface
    {
        $this->cache->save($this->getKey($key), $value);

        return $this;
    }

    public function has(string $key): bool
    {
        return (bool) $this->cache->load($this->getKey($key));
    }

    public function delete(string $key): void
    {
        $this->cache->remove($this->getKey($key));
    }
}