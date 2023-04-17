<?php

namespace nomit\Security\Session\Storage;

use nomit\Utility\ArrayObject;

/**
 * Array session storage
 *
 * Defines an ArrayObject interface for accessing session storage, with options
 * for setting metadata, locking, and marking as isImmutable.
 *
 * @see ReturnTypeWillChange
 */
class ArraySessionStorage extends ArrayObject implements SessionStorageInterface
{

    /**
     * Is storage marked isImmutable?
     *
     * @var bool
     */
    protected $isImmutable = false;

    /**
     * Constructor
     *
     * Instantiates storage as an ArrayObject, allowing property access.
     * Also sets the initial request access time.
     *
     * @param array  $input
     * @param int    $flags
     * @param string $iteratorClass
     */
    public function __construct(
        $input = [],
        $flags = ArrayObject::ARRAY_AS_PROPS,
        $iteratorClass = \ArrayIterator::class
    ) {
        parent::__construct($input, $flags, $iteratorClass);

        $this->setRequestAccessTime(microtime(true));
    }

    /**
     * Set the request access time
     *
     * @param  float        $time
     * @return \nomit\Security\Session\Storage\ArraySessionStorage
     */
    protected function setRequestAccessTime($time)
    {
        $this->setMetadata('_REQUEST_ACCESS_TIME', $time);

        return $this;
    }

    /**
     * Retrieve the request access time
     *
     * @return float
     */
    public function getRequestAccessTime()
    {
        return $this->getMetadata('_REQUEST_ACCESS_TIME');
    }

    /**
     * Set a value in the storage object
     *
     * If the object is marked as isImmutable, or the object or key is marked as
     * locked, raises an exception.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */

    /**
     * @param  mixed                      $key
     * @param  mixed                      $value
     * @throws \RuntimeException
     */
    #[ReturnTypeWillChange]
    public function offsetSet($key, $value): void
    {
        if ($this->isImmutable()) {
            throw new \RuntimeException(
                sprintf('Cannot set key "%s" as storage is marked isImmutable', $key)
            );
        }
        if ($this->isLocked($key)) {
            throw new \RuntimeException(
                sprintf('Cannot set key "%s" due to locking', $key)
            );
        }

        parent::offsetSet($key, $value);
    }

    /**
     * Lock this storage instance, or a key within it
     *
     * @param  null|int|string $key
     * @return ArraySessionStorage
     */
    public function lock($key = null)
    {
        if (null === $key) {
            $this->setMetadata('_READONLY', true);

            return $this;
        }
        if (isset($this[$key])) {
            $this->setMetadata('_LOCKS', [$key => true]);
        }

        return $this;
    }

    /**
     * Is the object or key marked as locked?
     *
     * @param  null|int|string $key
     * @return bool
     */
    public function isLocked($key = null)
    {
        if ($this->isImmutable()) {
            // isImmutable trumps all
            return true;
        }

        if (null === $key) {
            // testing for global lock
            return $this->getMetadata('_READONLY');
        }

        $locks    = $this->getMetadata('_LOCKS');
        $readOnly = $this->getMetadata('_READONLY');

        if ($readOnly && ! $locks) {
            // global lock in play; all keys are locked
            return true;
        }

        if ($readOnly && $locks) {
            return array_key_exists($key, $locks);
        }

        // test for individual locks
        if (! $locks) {
            return false;
        }

        return array_key_exists($key, $locks);
    }

    /**
     * Unlock an object or key marked as locked
     *
     * @param  null|int|string $key
     * @return ArraySessionStorage
     */
    public function unlock($key = null)
    {
        if (null === $key) {
            // Unlock everything
            $this->setMetadata('_READONLY', false);
            $this->setMetadata('_LOCKS', false);

            return $this;
        }

        $locks = $this->getMetadata('_LOCKS');
        if (! $locks) {
            if (! $this->getMetadata('_READONLY')) {
                return $this;
            }
            $array = $this->toArray();
            $keys  = array_keys($array);
            $locks = array_flip($keys);
            unset($array, $keys);
        }

        if (array_key_exists($key, $locks)) {
            unset($locks[$key]);
            $this->setMetadata('_LOCKS', $locks, true);
        }

        return $this;
    }

    /**
     * Mark the storage container as isImmutable
     *
     * @return ArraySessionStorage
     */
    public function markImmutable()
    {
        $this->isImmutable = true;

        return $this;
    }

    /**
     * Is the storage container marked as isImmutable?
     *
     * @return bool
     */
    public function isImmutable()
    {
        return $this->isImmutable;
    }

    /**
     * Set storage metadata
     *
     * Metadata is used to store information about the data being stored in the
     * object. Some example use cases include:
     * - Setting expiry data
     * - Maintaining access counts
     * - localizing session storage
     * - etc.
     *
     * @param  string                     $key
     * @param  mixed                      $value
     * @param  bool                       $overwriteArray Whether to overwrite or merge array values; by default, merges
     * @return ArraySessionStorage
     * @throws \RuntimeException
     */
    public function setMetadata($key, $value, $overwriteArray = false)
    {
        if ($this->isImmutable) {
            throw new \RuntimeException(
                sprintf('Cannot set key "%s" as storage is marked isImmutable', $key)
            );
        }

        if (! isset($this['__Nomit'])) {
            $this['__Nomit'] = [];
        }

        if (isset($this['__Nomit'][$key]) && is_array($value)) {
            if ($overwriteArray) {
                $this['__Nomit'][$key] = $value;
            } else {
                $this['__Nomit'][$key] = array_replace_recursive($this['__Nomit'][$key], $value);
            }
        } else {
            if ((null === $value) && isset($this['__Nomit'][$key])) {
                // unset($this['__Nomit'][$key]) led to "indirect modification...
                // has no effect" errors, so explicitly pulling array and
                // unsetting key.
                $array = $this['__Nomit'];
                unset($array[$key]);
                $this['__Nomit'] = $array;
                unset($array);
            } elseif (null !== $value) {
                $this['__Nomit'][$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Retrieve metadata for the storage object or a specific metadata key
     *
     * Returns false if no metadata stored, or no metadata exists for the given
     * key.
     *
     * @param  null|int|string $key
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if (! isset($this['__Nomit'])) {
            return false;
        }

        if (null === $key) {
            return $this['__Nomit'];
        }

        if (! array_key_exists($key, $this['__Nomit'])) {
            return false;
        }

        return $this['__Nomit'][$key];
    }

    /**
     * Clear the storage object or a subkey of the object
     *
     * @param  null|int|string            $key
     * @return ArraySessionStorage
     * @throws \RuntimeException
     */
    public function clear($key = null)
    {
        if ($this->isImmutable()) {
            throw new \RuntimeException('Cannot clear storage as it is marked immutable');
        }

        if (null === $key) {
            $this->fromArray([]);

            return $this;
        }

        if (! isset($this[$key])) {
            return $this;
        }

        // Clear key data
        unset($this[$key]);

        // Clear key metadata
        $this->setMetadata($key, null)
            ->unlock($key);

        return $this;
    }

    /**
     * Load the storage from another array
     *
     * Overwrites any data that was previously set.
     *
     * @param  array        $array
     * @return ArraySessionStorage
     */
    public function fromArray(array $array)
    {
        $ts = $this->getRequestAccessTime();

        $this->exchangeArray($array);
        $this->setRequestAccessTime($ts);

        return $this;
    }

    /**
     * Cast the object to an array
     *
     * @param  bool $metaData Whether to include metadata
     * @return array
     */
    public function toArray($metaData = false)
    {
        $values = $this->getArrayCopy();
        if ($metaData) {
            return $values;
        }
        if (isset($values['__Nomit'])) {
            unset($values['__Nomit']);
        }

        return $values;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if(($result = $this->offsetGet($key)) !== null) {
            return $result;
        }

        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, mixed $value): self
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove(string $key): self
    {
        $this->offsetUnset($key);

        return $this;
    }

}