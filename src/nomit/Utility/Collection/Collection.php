<?php

namespace nomit\Utility\Collection;

use ArrayAccess;
use ArrayIterator;
use CachingIterator;
use nomit\Resource\Manifest\Store\Manifest\ManifestInterface;
use Closure;
use Countable;
use Exception;
use InvalidArgumentException;
use IteratorAggregate;
use JsonException;
use JsonSerializable;
use nomit\Utility\Arrays;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Jsonable;
use nomit\Utility\Collection\CollectionInterface;
use nomit\Utility\Tap\Proxy;
use nomit\Utility\Object\MacroableTrait;
use stdClass;
use Traversable;
use function nomit\array_get;
use function nomit\value;

/**
 * Class Collection
 *  Data wrapper/container that facilitates the interaction and manipulation thereof.
 *
 * For the original documentation:
 * @see https://laravel.com/docs/8.x/collections
 *
 * For the original code from Laravel, from which this is derived:
 * @see https://github.com/jimrubenstein/laravel-framework/blob/master/src/Illuminate/Support/Collection.php
 *
 * @package Illuminate\Support
 */
class Collection implements CollectionInterface
{

    use MacroableTrait;

    /**
     * The methods that can be proxied.
     *
     * @var array
     */
    protected static array $proxies = [
        'average', 'avg', 'contains', 'each', 'every', 'filter', 'first', 'flatMap',
        'keyBy', 'map', 'partition', 'reject', 'sortBy', 'sortByDesc', 'sum', 'unique',
    ];
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected array $collection = [];

    /**
     * Create a new collection.
     *
     * @param mixed $items
     * @return void
     */
    public function __construct(mixed $items = [])
    {
        $this->collection = $this->getArrayableItems($items);
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param mixed $items
     * @return array
     */
    protected function getArrayableItems(mixed $items)
    {
        if (is_array($items)) {
            return $items;
        }

        if ($items instanceof self) {
            return $items->all();
        }

        if ($items instanceof Arrayable) {
            return $items->toArray();
        }

        if ($items instanceof Jsonable) {
            return json_decode($items->toJson(), true);
        }

        if ($items instanceof JsonSerializable) {
            return $items->jsonSerialize();
        }

        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array)$items;
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->collection;
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @param mixed $items
     * @return static
     */
    public static function make(mixed $items = [])
    {
        return new static($items);
    }

    /**
     * Wrap the given value in a collection if applicable.
     *
     * @param mixed $value
     * @return static
     */
    public static function wrap(mixed $value)
    {
        return $value instanceof self
            ? new static($value)
            : new static(Arrays::wrap($value));
    }

    /**
     * Get the underlying items from the given collection if applicable.
     *
     * @param array|static $value
     * @return array
     */
    public static function unwrap(array|self $value)
    {
        return $value instanceof self ? $value->all() : $value;
    }

    /**
     * Create a new collection by invoking the callback a given amount of times.
     *
     * @param int $number
     * @param callable $callback
     * @return static
     */
    public static function times(int $number, callable $callback = null)
    {
        if ($number < 1) {
            return new static;
        }

        if (is_null($callback)) {
            return new static(range(1, $number));
        }

        return (new static(range(1, $number)))->map($callback);
    }

    /**
     * Run a map over each of the items.
     *
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback)
    {
        $keys = array_keys($this->collection);

        $items = array_map($callback, $this->collection, $keys);

        return new static(array_combine($keys, $items));
    }

    /**
     * Add a method to the list of proxied methods.
     *
     * @param string $method
     * @return void
     */
    public static function proxy($method)
    {
        static::$proxies[] = $method;
    }

    /**
     * Get the median of a given key.
     *
     * @param null $key
     * @return mixed
     */
    public function median(mixed $key = null)
    {
        $count = $this->count();

        if ($count == 0) {
            return null;
        }

        $values = (isset($key) ? $this->pluck($key) : $this)
            ->sort()->values();

        $middle = (int)($count / 2);

        if ($count % 2) {
            return $values->get($middle);
        }

        return (new static([
            $values->get($middle - 1), $values->get($middle),
        ]))->average();
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static
     */
    public function values()
    {
        return new static(array_values($this->collection));
    }

    /**
     * Sort through each item with a callback.
     *
     * @param callable|null $callback
     * @return static
     */
    public function sort(callable $callback = null)
    {
        $items = $this->collection;

        $callback
            ? uasort($items, $callback)
            : asort($items);

        return new static($items);
    }

    /**
     * Get the values of a given key.
     *
     * @param string|array $value
     * @param null $key
     * @return static
     */
    public function pluck(array|string $value, string $key = null)
    {
        return new static(Arrays::pluck($this->collection, $value, $key));
    }

    /**
     * Get an item from the collection by key.
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get(mixed $key, mixed $default = null)
    {
        if ($this->offsetExists($key)) {
            return Arrays::get($this->collection, $key);
        }

        return $default instanceof \Closure ? $default() : $default;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return Arrays::has($this->collection, $key);
    }

    /**
     * Alias for the "avg" method.
     *
     * @param callable|string|null $callback
     * @return mixed
     */
    public function average(callable|string $callback = null)
    {
        return $this->avg($callback);
    }

    /**
     * Get the average value of a given key.
     *
     * @param callable|string|null $callback
     * @return mixed
     */
    public function avg(callable|string $callback = null)
    {
        if ($count = $this->count()) {
            return $this->sum($callback) / $count;
        }

        return null;
    }

    /**
     * Get the sum of the given values.
     *
     * @param callable|string|null $callback
     * @return mixed
     */
    public function sum(callable|string $callback = null)
    {
        if (is_null($callback)) {
            return array_sum($this->collection);
        }

        $callback = $this->value_retriever($callback);

        return $this->reduce(function ($result, $item) use ($callback) {
            return $result + $callback($item);
        }, 0);
    }

    /**
     * Get a value retrieving callback.
     *
     * @param string $value
     * @return callable
     */
    protected function value_retriever($value)
    {
        if ($this->useAsCallable($value)) {
            return $value;
        }

        return function ($item) use ($value) {
            return array_get($item, $value);
        };
    }

    /**
     * Determine if the given value is callable, but not a string.
     *
     * @param mixed $value
     * @return bool
     */
    protected function useAsCallable(mixed $value)
    {
        return !is_string($value) && is_callable($value);
    }

    /**
     * Reduce the collection to a single value.
     *
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, mixed $initial = null)
    {
        return array_reduce($this->collection, $callback, $initial);
    }

    /**
     * Get the mode of a given key.
     *
     * @param mixed $key
     * @return array|null
     */
    public function mode(mixed $key = null)
    {
        $count = $this->count();

        if ($count == 0) {
            return null;
        }

        $collection = isset($key) ? $this->pluck($key) : $this;

        $counts = new self;

        $collection->each(function ($value) use ($counts) {
            $counts[$value] = isset($counts[$value]) ? $counts[$value] + 1 : 1;
        });

        $sorted = $counts->sort();

        $highestValue = $sorted->last();

        return $sorted->filter(function ($value) use ($highestValue) {
            return $value == $highestValue;
        })->sort()->keys()->all();
    }

    /**
     * Execute a callback over each item.
     *
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this->collection as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Get the last item from the collection.
     *
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public function last(callable $callback = null, mixed $default = null)
    {
        return Arrays::last($this->collection, $callback, $default);
    }

    /**
     * Get the keys of the collection items.
     *
     * @return static
     */
    public function keys()
    {
        return new static(array_keys($this->collection));
    }

    /**
     * Run a filter over each of the items.
     *
     * @param callable|null $callback
     * @return static
     */
    public function filter(callable $callback = null)
    {
        if ($callback) {
            return new static(Arrays::where($this->collection, $callback));
        }

        return new static(array_filter($this->collection));
    }

    /**
     * Determine if an item exists in the collection using strict comparison.
     *
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    public function containsStrict(mixed $key, mixed $value = null)
    {
        if (func_num_args() == 2) {
            return $this->contains(function ($item) use ($key, $value) {
                return array_get($item, $key) === $value;
            });
        }

        if ($this->useAsCallable($key)) {
            return !is_null($this->first($key));
        }

        return in_array($key, $this->collection, true);
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function contains(mixed $key, mixed $operator = null, mixed $value = null)
    {
        if (func_num_args() == 1) {
            if ($this->useAsCallable($key)) {
                $placeholder = new stdClass();

                return $this->first($key, $placeholder) !== $placeholder;
            }

            return in_array($key, $this->collection);
        }

        return $this->contains($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Get the first item from the collection.
     *
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public function first(callable $callback = null, $default = null)
    {
        return Arrays::first($this->collection, $callback, $default);
    }

    /**
     * Get an operator checker callback.
     *
     * @param string $key
     * @param string $operator
     * @param mixed $value
     * @return Closure
     */
    protected function operatorForWhere(string $key, string $operator, mixed $value = null)
    {
        if (func_num_args() == 2) {
            $value = $operator;

            $operator = '=';
        }

        return function ($item) use ($key, $operator, $value) {
            $retrieved = array_get($item, $key);

            $strings = array_filter([$retrieved, $value], function ($value) {
                return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
            });

            if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
                return in_array($operator, ['!=', '<>', '!==']);
            }

            switch ($operator) {
                default:
                case '=':
                case '==':
                    return $retrieved == $value;
                case '!=':
                case '<>':
                    return $retrieved != $value;
                case '<':
                    return $retrieved < $value;
                case '>':
                    return $retrieved > $value;
                case '<=':
                    return $retrieved <= $value;
                case '>=':
                    return $retrieved >= $value;
                case '===':
                    return $retrieved === $value;
                case '!==':
                    return $retrieved !== $value;
            }
        };
    }

    /**
     * Cross join with the given lists, returning all possible permutations.
     *
     * @param mixed ...$lists
     * @return static
     */
    public function crossJoin(...$lists)
    {
        return new static(Arrays::crossJoin(
            $this->collection, ...array_map([$this, 'getArrayableItems'], $lists)
        ));
    }

    /**
     * Get the items in the collection that are not present in the given items.
     *
     * @param mixed $items
     * @return static
     */
    public function difference(mixed $items)
    {
        return new static(array_diff($this->collection, $this->getArrayableItems($items)));
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items.
     *
     * @param mixed $items
     * @return static
     */
    public function differenceAssociative(mixed $items)
    {
        return new static(array_diff_assoc($this->collection, $this->getArrayableItems($items)));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * @param mixed $items
     * @return static
     */
    public function differenceKeys(mixed $items)
    {
        return new static(array_diff_key($this->collection, $this->getArrayableItems($items)));
    }

    /**
     * Execute a callback over each nested chunk of items.
     *
     * @param callable $callback
     * @return static
     */
    public function eachSpread(callable $callback)
    {
        return $this->each(function ($chunk, $key) use ($callback) {
            $chunk[] = $key;

            return $callback(...$chunk);
        });
    }

    /**
     * Determine if all items in the collection pass the given test.
     *
     * @param string|callable $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function every(string|callable $key, mixed $operator = null, mixed $value = null)
    {
        if (func_num_args() == 1) {
            $callback = $this->value_retriever($key);

            foreach ($this->collection as $k => $v) {
                if (!$callback($v, $k)) {
                    return false;
                }
            }

            return true;
        }

        return $this->every($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param self|mixed $keys
     * @return static
     */
    public function except(mixed $keys)
    {
        if ($keys instanceof self) {
            $keys = $keys->all();
        } elseif (!is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(Arrays::except($this->collection, $keys));
    }

    /**
     * Apply the callback if the value is falsy.
     *
     * @param bool $value
     * @param callable $callback
     * @param callable|null $default
     * @return mixed
     */
    public function unless(bool $value, callable $callback, callable $default = null)
    {
        return $this->when(!$value, $callback, $default);
    }

    /**
     * Apply the callback if the value is truthy.
     *
     * @param bool $value
     * @param callable $callback
     * @param callable|null $default
     * @return mixed
     */
    public function when(bool $value, callable $callback, callable $default = null)
    {
        if ($value) {
            return $callback($this, $value);
        }

        if ($default) {
            return $default($this, $value);
        }

        return $this;
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function whereStrict(string $key, mixed $value)
    {
        return $this->where($key, '===', $value);
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param string $key
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where(string $key, mixed $operator, mixed $value = null)
    {
        return $this->filter($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param string $key
     * @param mixed $values
     * @return static
     */
    public function whereInStrict(string $key, mixed $values)
    {
        return $this->whereIn($key, $values, true);
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param string $key
     * @param mixed $values
     * @param bool $strict
     * @return static
     */
    public function whereIn(string $key, mixed $values, bool $strict = false)
    {
        $values = $this->getArrayableItems($values);

        return $this->filter(function ($item) use ($key, $values, $strict) {
            return in_array(array_get($item, $key), $values, $strict);
        });
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param string $key
     * @param mixed $values
     * @return static
     */
    public function whereNotInStrict(string $key, mixed $values)
    {
        return $this->whereNotIn($key, $values, true);
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param string $key
     * @param mixed $values
     * @param bool $strict
     * @return static
     */
    public function whereNotIn(string $key, mixed $values, bool $strict = false)
    {
        $values = $this->getArrayableItems($values);

        return $this->reject(function ($item) use ($key, $values, $strict) {
            return in_array(array_get($item, $key), $values, $strict);
        });
    }

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param callable|mixed $callback
     * @return static
     */
    public function reject(mixed $callback)
    {
        if ($this->useAsCallable($callback)) {
            return $this->filter(function ($value, $key) use ($callback) {
                return !$callback($value, $key);
            });
        }

        return $this->filter(function ($item) use ($callback) {
            return $item != $callback;
        });
    }

    /**
     * Get the first item by the given key value pair.
     *
     * @param string $key
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function firstWhere(string $key, mixed $operator, mixed $value = null)
    {
        return $this->first($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param int|float $depth
     * @return static
     */
    public function flatten(int|float $depth = INF)
    {
        return new static(Arrays::flatten($this->collection, $depth));
    }

    /**
     * Flip the items in the collection.
     *
     * @return static
     */
    public function flip()
    {
        return new static(array_flip($this->collection));
    }

    /**
     * @alias
     * @param string|array $keys
     * @return $this
     * @see forget()
     */
    public function remove(string|array $keys)
    {
        return $this->forget($keys);
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param string|array $keys
     * @return $this
     */
    public function forget(string|array $keys)
    {
        foreach ((array)$keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        Arrays::forget($this->collection, $key);
    }

    /**
     * Group an associative array by a field or using a callback.
     *
     * @param callable|string $group_by
     * @param bool $preserve_keys
     * @return static
     */
    public function groupBy(callable|string $group_by, bool $preserve_keys = false)
    {
        if (is_array($group_by)) {
            $next_groups = $group_by;

            $group_by = array_shift($next_groups);
        }

        $group_by = $this->value_retriever($group_by);

        $results = [];

        foreach ($this->collection as $key => $value) {
            $group_keys = $group_by($value, $key);

            if (!is_array($group_keys)) {
                $group_keys = [$group_keys];
            }

            foreach ($group_keys as $group_key) {
                $group_key = is_bool($group_key) ? (int)$group_key : $group_key;

                if (!array_key_exists($group_key, $results)) {
                    $results[$group_key] = new static;
                }

                $results[$group_key]->offsetSet($preserve_keys ? $key : null, $value);
            }
        }

        $result = new static($results);

        if (!empty($next_groups)) {
            return $result->map->groupBy($next_groups, $preserve_keys);
        }

        return $result;
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->collection = Arrays::append($this->collection, $key, $value);
        } else {
            Arrays::set($this->collection, $key, $value);
        }
    }

    /**
     * Key an associative array by a field or using a callback.
     *
     * @param callable|string $key_by
     * @return static
     */
    public function keyBy(callable|string $key_by)
    {
        $key_by = $this->value_retriever($key_by);

        $results = [];

        foreach ($this->collection as $key => $item) {
            $resolvedKey = $key_by($item, $key);

            if (is_object($resolvedKey)) {
                $resolvedKey = (string)$resolvedKey;
            }

            $results[$resolvedKey] = $item;
        }

        return new static($results);
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param mixed $key
     * @return bool
     */
    public function has(mixed $key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if (!$this->offsetExists($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param string $value
     * @param string|null $glue
     * @return string
     */
    public function implode(string $value, string $glue = null)
    {
        $first = $this->first();

        if (is_array($first) || is_object($first)) {
            return implode($glue, $this->pluck($value)->all());
        }

        return implode($value, $this->collection);
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param mixed $items
     * @return static
     */
    public function intersect(mixed $items)
    {
        return new static(array_intersect($this->collection, $this->getArrayableItems($items)));
    }

    /**
     * Intersect the collection with the given items by key.
     *
     * @param mixed $items
     * @return static
     */
    public function IntersectByKeys(mixed $items)
    {
        return new static(array_intersect_key(
            $this->collection, $this->getArrayableItems($items)
        ));
    }

    /**
     * Determine if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->collection);
    }

    /**
     * Run a map over each nested chunk of items.
     *
     * @param callable $callback
     * @return static
     */
    public function mapSpread(callable $callback)
    {
        return $this->map(function ($chunk, $key) use ($callback) {
            $chunk[] = $key;

            return $callback(...$chunk);
        });
    }

    /**
     * Run a grouping map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @param callable $callback
     * @return static
     */
    public function mapToGroups(callable $callback)
    {
        $groups = $this->mapToDictionary($callback);

        return $groups->map([$this, 'make']);
    }

    /**
     * Run a dictionary map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @param callable $callback
     * @return static
     */
    public function mapToDictionary(callable $callback)
    {
        $dictionary = $this->map($callback)->reduce(function ($groups, $pair) {
            $groups[key($pair)][] = reset($pair);

            return $groups;
        }, []);

        return new static($dictionary);
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @param callable $callback
     * @return static
     */
    public function mapWithKeys(callable $callback)
    {
        $result = [];

        foreach ($this->collection as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return new static($result);
    }

    /**
     * Map a collection and flatten the result by a single level.
     *
     * @param callable $callback
     * @return static
     */
    public function mapAndFlatten(callable $callback)
    {
        return $this->map($callback)->collapse();
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return static
     */
    public function collapse()
    {
        return new static(Arrays::collapse($this->collection));
    }

    /**
     * Map the values into a new class.
     *
     * @param string $class
     * @return static
     */
    public function map_into(string $class)
    {
        return $this->map(function ($value, $key) use ($class) {
            return new $class($value, $key);
        });
    }

    /**
     * Get the max value of a given key.
     *
     * @param callable|string|null $callback
     * @return mixed
     */
    public function max(callable|string $callback = null)
    {
        $callback = $this->value_retriever($callback);

        return $this->filter(function ($value) {
            return !is_null($value);
        })->reduce(function ($result, $item) use ($callback) {
            $value = $callback($item);

            return is_null($result) || $value > $result ? $value : $result;
        });
    }

    /**
     * Merge the collection with the given items.
     *
     * @param mixed $items
     * @return static
     */
    public function merge(mixed $items)
    {
        return new static(array_merge($this->collection, $this->getArrayableItems($items)));
    }

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @param mixed $values
     * @return static
     */
    public function combine(mixed $values)
    {
        return new static(array_combine($this->all(), $this->getArrayableItems($values)));
    }

    /**
     * Union the collection with the given items.
     *
     * @param mixed $items
     * @return static
     */
    public function union(mixed $items)
    {
        return new static($this->collection + $this->getArrayableItems($items));
    }

    /**
     * Get the min value of a given key.
     *
     * @param callable|string|null $callback
     * @return mixed
     */
    public function min(callable|string $callback = null)
    {
        $callback = $this->value_retriever($callback);

        return $this->filter(function ($value) {
            return !is_null($value);
        })->reduce(function ($result, $item) use ($callback) {
            $value = $callback($item);

            return is_null($result) || $value < $result ? $value : $result;
        });
    }

    /**
     * Create a new collection consisting of every n-th element.
     *
     * @param int $step
     * @param int $offset
     * @return static
     */
    public function nth(int $step, int $offset = 0)
    {
        $new = [];

        $position = 0;

        foreach ($this->collection as $item) {
            if ($position % $step === $offset) {
                $new[] = $item;
            }

            $position++;
        }

        return new static($new);
    }

    /**
     * Get the items with the specified keys.
     *
     * @param mixed $keys
     * @return static
     */
    public function only(mixed $keys)
    {
        if (is_null($keys)) {
            return new static($this->collection);
        }

        if ($keys instanceof self) {
            $keys = $keys->all();
        }

        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(Arrays::only($this->collection, $keys));
    }

    /**
     * "Paginate" the collection by slicing it into a smaller collection.
     *
     * @param int $page
     * @param int $per_page
     * @return static
     */
    public function forPage(int $page, int $per_page)
    {
        $offset = max(0, ($page - 1) * $per_page);

        return $this->slice($offset, $per_page);
    }

    /**
     * Slice the underlying collection array.
     *
     * @param int $offset
     * @param int $length
     * @return static
     */
    public function slice($offset, $length = null)
    {
        return new static(array_slice($this->collection, $offset, $length, true));
    }

    /**
     * Partition the collection into two arrays using the given callback or key.
     *
     * @param callable|string $callback
     * @return static
     */
    public function partition(callable|string $callback)
    {
        $partitions = [new static, new static];

        $callback = $this->value_retriever($callback);

        foreach ($this->collection as $key => $item) {
            $partitions[(int)!$callback($item, $key)][$key] = $item;
        }

        return new static($partitions);
    }

    /**
     * Pass the collection to the given callback and return the result.
     *
     * @param callable $callback
     * @return mixed
     */
    public function pipe(callable $callback)
    {
        return $callback($this);
    }

    /**
     * Get and remove the last item from the collection.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->collection);
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param mixed $value
     * @param mixed $key
     * @return $this
     * @see Arrays::prepend()
     */
    public function prepend(mixed $value, mixed $key = null)
    {
        $this->collection = Arrays::prepend($this->collection, $value, $key);

        return $this;
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param mixed $value
     * @param mixed $key
     * @return $this
     * @see Arrays::append()
     */
    public function append(mixed $value, mixed $key = null)
    {
        $this->collection = Arrays::append($this->collection, $value, $key);

        return $this;
    }

    /**
     * Push all of the given items onto the collection.
     *
     * @param Traversable $source
     * @return $this
     */
    public function concate(Traversable $source)
    {
        $result = new static($this);

        foreach ($source as $item) {
            $result->push($item);
        }

        return $result;
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param mixed $value
     * @return $this
     */
    public function push(mixed $value)
    {
        $this->offsetSet(null, $value);

        return $this;
    }

    /**
     * Get and remove an item from the collection.
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function pull(mixed $key, mixed $default = null)
    {
        return Arrays::pull($this->collection, $key, $default);
    }

    /**
     * @alias
     * @param mixed $key
     * @param mixed $value
     * @return $this
     * @see put()
     */
    public function set(mixed $key, mixed $value)
    {
        return $this->put($key, $value);
    }

    /**
     * Put an item in the collection by key.
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function put(mixed $key, mixed $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param int|null $number
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function random(int $number = null)
    {
        if (is_null($number)) {
            return Arrays::random($this->collection);
        }

        return new static(Arrays::random($this->collection, $number));
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse()
    {
        return new static(array_reverse($this->collection, true));
    }

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param mixed $value
     * @param bool $strict
     * @return mixed
     */
    public function search(mixed $value, bool $strict = false)
    {
        if (!$this->useAsCallable($value)) {
            return array_search($value, $this->collection, $strict);
        }

        foreach ($this->collection as $key => $item) {
            if ($value($item, $key)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Get and remove the first item from the collection.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->collection);
    }

    /**
     * Shuffle the items in the collection.
     *
     * @param int|null $seed
     * @return static
     * @throws Exception
     */
    public function shuffle(int $seed = null)
    {
        $items = $this->collection;

        if (is_null($seed)) {
            shuffle($items);
        } else {
            mt_srand($seed);

            usort($items, function () {
                return random_int(-1, 1);
            });
        }

        return new static($items);
    }

    /**
     * Split a collection into a certain number of groups.
     *
     * @param int $number_of_groups
     * @return static
     */
    public function split($number_of_groups)
    {
        if ($this->isEmpty()) {
            return new static;
        }

        $groupSize = ceil($this->count() / $number_of_groups);

        return $this->chunk($groupSize);
    }

    /**
     * Chunk the underlying collection array.
     *
     * @param int $size
     * @return static
     */
    public function chunk($size)
    {
        if ($size <= 0) {
            return new static;
        }

        $chunks = [];

        foreach (array_chunk($this->collection, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param callable|string $callback
     * @param int $options
     * @return static
     */
    public function sortByDescending(callable|string $callback, int $options = SORT_REGULAR)
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param callable|string $callback
     * @param int $options
     * @param bool $descending
     * @return static
     */
    public function sortBy(callable|string $callback, int $options = SORT_REGULAR,
                           bool $descending = false)
    {
        $results = [];

        $callback = $this->value_retriever($callback);

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // and grab the corresponding values for the sorted keys from this array.
        foreach ($this->collection as $key => $value) {
            $results[$key] = $callback($value, $key);
        }

        $descending ? arsort($results, $options)
            : asort($results, $options);

        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $this->collection[$key];
        }

        return new static($results);
    }

    /**
     * Splice a portion of the underlying collection array.
     *
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @return static
     */
    public function splice(int $offset, int $length = null, mixed $replacement = [])
    {
        if (func_num_args() === 1) {
            return new static(array_splice($this->collection, $offset));
        }

        return new static(array_splice($this->collection, $offset, $length, $replacement));
    }

    /**
     * Take the first or last {$limit} items.
     *
     * @param int $limit
     * @return static
     */
    public function take(int $limit)
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }

        return $this->slice(0, $limit);
    }

    /**
     * Pass the collection to the given callback and then return it.
     *
     * @param callable $callback
     * @return $this
     */
    public function tap(callable $callback)
    {
        $callback(new static($this->collection));

        return $this;
    }

    /**
     * Transform each item in the collection using a callback.
     *
     * @param callable $callback
     * @return $this
     */
    public function transform(callable $callback)
    {
        $this->collection = $this->map($callback)->all();

        return $this;
    }

    /**
     * Return only unique items from the collection array using strict comparison.
     *
     * @param string|callable|null $key
     * @return static
     */
    public function uniqueStrict($key = null)
    {
        return $this->unique($key, true);
    }

    /**
     * Return only unique items from the collection array.
     *
     * @param string|callable|null $key
     * @param bool $strict
     * @return static
     */
    public function unique($key = null, $strict = false)
    {
        if (is_null($key)) {
            return new static(array_unique($this->collection, SORT_REGULAR));
        }

        $callback = $this->value_retriever($key);

        $exists = [];

        return $this->reject(function ($item, $key) use ($callback, $strict, &$exists) {
            if (in_array($id = $callback($item, $key), $exists, $strict)) {
                return true;
            }

            $exists[] = $id;
        });
    }

    /**
     * Zip the collection together with one or more arrays.
     *
     * e.g. new Collection([3, 2, 3])->zip([4, 5, 6]);
     *      => [[3, 4], [2, 5], [3, 6]]
     *
     * @param mixed ...$items
     * @return static
     */
    public function zip(mixed ...$items)
    {
        $arrayable_items = array_map(function ($items) {
            return $this->getArrayableItems($items);
        }, func_get_args());

        $params = array_merge([function () {
            return new static(func_get_args());
        }, $this->collection], $arrayable_items);

        return new static(array_map(...$params));
    }

    /**
     * Pad collection to the specified length with a value.
     *
     * @param int $size
     * @param mixed $value
     * @return static
     */
    public function pad(int $size, mixed $value)
    {
        return new static(array_pad($this->collection, $size, $value));
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @param array|Arrayable|null $data
     * @return array
     */
    public function toArray(array|Arrayable $data = null): array
    {
        if(is_null($data)) {
            $data = $this->collection;
        }

        foreach ($data as $key => $value) {
            if(is_array($value)) {
                $data[$key] = $this->toArray($value);
            } elseif($value instanceof Arrayable) {
                $data[$key] = $this->toArray($value->toArray());
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function __toArray(): array
    {
        return $this->toArray();
    }

    /**
     * Get a CachingIterator instance.
     *
     * @param int $flags
     * @return CachingIterator
     */
    public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING)
    {
        return new CachingIterator($this->getIterator(), $flags);
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return self
     */
    public function toBase()
    {
        return new self($this);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return Arrays::get($this->collection, $key);
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     * @throws JsonException
     */
    public function jsonSerialize(): mixed
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            }

            if ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            }

            if ($value instanceof Arrayable) {
                return $value->toArray();
            }

            return $value;
        }, $this->collection);
    }

    /**
     * Dynamically access collection proxies.
     *
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function __get(string $key)
    {
        if (!in_array($key, static::$proxies)) {
            throw new Exception('No property keyed {{' . $key . '}} exists in this ' .
                '{{' . __CLASS__ . '}} instance.');
        }

        return new Proxy($this, $key);
    }

}