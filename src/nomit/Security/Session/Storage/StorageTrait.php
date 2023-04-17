<?php

namespace nomit\Security\Session\Storage;

use nomit\Dumper\Dumper;

/**
 * Trait StorageAwareTrait
 * @package nomit\security\Session\Storage
 */
trait StorageTrait
{

    /**
     * @var SessionStorageInterface
     */
    protected $storage;

    /**
     * @param SessionStorageInterface $storage
     * @return \nomit\Security\Session\Storage\StorageTrait
     */
    public function setStorage(SessionStorageInterface $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return ?SessionStorageInterface
     */
    public function getStorage(): ?SessionStorageInterface
    {
        return $this->storage;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->storage->has($key);
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->storage->get($key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, mixed $value): self
    {
        $this->storage->set($key, $value);

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->storage->all();
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove(string $key): void
    {
        $this->storage->remove($key);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setAll(array $data): self
    {
        $this->storage->fromArray($data);

        return $this;
    }

    public function clear(): void
    {
        $this->storage->clear();
    }

}