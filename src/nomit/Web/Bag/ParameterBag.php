<?php

namespace nomit\Web\Bag;

use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use nomit\Utility\Arrays;
use nomit\Web\Exception\BadRequestException;
use function array_key_exists;
use function count;
use function func_num_args;
use function is_array;
use const FILTER_CALLBACK;
use const FILTER_DEFAULT;
use const FILTER_REQUIRE_ARRAY;
use const FILTER_SANITIZE_NUMBER_INT;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * ParameterBag is a container for key/value pairs.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ParameterBag implements BagInterface
{

    /**
     * Parameter storage.
     */
    protected $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the parameters.
     *
     * @param string|null $key The name of the parameter to return or null to get them all
     *
     * @return array An array of parameters
     */
    public function all(?string $key = null): array
    {
        if (null === $key) {
            return $this->parameters;
        }

        if (!is_array($value = $this->parameters[$key] ?? [])) {
            throw new BadRequestException(sprintf('Unexpected value for parameter "%s": expecting "array", got "%s".', $key, get_debug_type($value)));
        }

        return $value;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys(): array
    {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     */
    public function replace(array $parameters = []): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Adds parameters.
     */
    public function add(array $parameters = []): self
    {
        $this->parameters = array_replace($this->parameters, $parameters);

        return $this;
    }

    /**
     * Sets a parameter by name.
     *
     * @param mixed $value The value
     */
    public function set(string $key, mixed $value): self
    {
        Arrays::set($this->parameters, $key, $value);

        return $this;
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has(string $key): bool
    {
        return Arrays::has($this->parameters, $key);
    }

    /**
     * Removes a parameter.
     */
    public function remove(string $key): void
    {
        Arrays::remove($this->parameters, $key);
    }

    /**
     * Returns the alphabetic characters of the parameter value.
     *
     * @return string The filtered value
     */
    public function getAlphabetic(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }

    /**
     * Returns a parameter by name.
     *
     * @param mixed $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arrays::get($this->parameters, $key, $default);
    }

    /**
     * Returns the alphabetic characters and digits of the parameter value.
     *
     * @return string The filtered value
     */
    public function getAlphanumeric(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }

    /**
     * Returns the digits of the parameter value.
     *
     * @return string The filtered value
     */
    public function getDigits(string $key, string $default = ''): string
    {
        // we need to remove - and + because they're allowed in the filter
        return str_replace(['-', '+'], '', $this->filter($key, $default, FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Filter key.
     *
     * @param mixed $default Default = null
     * @param int $filter FILTER_* constant
     * @param mixed $options Filter options
     *
     * @return mixed
     * @see https://php.net/filter-var
     *
     */
    public function filter(string $key, mixed $default = null, int $filter = FILTER_DEFAULT, array $options = [])
    {
        $value = $this->get($key, $default);

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        // Add a convenience check for arrays.
        if (is_array($value) && !isset($options['flags'])) {
            $options['flags'] = FILTER_REQUIRE_ARRAY;
        }

        return filter_var($value, $filter, $options);
    }

    /**
     * Returns the parameter value converted to integer.
     *
     * @return int The filtered value
     */
    public function getInteger(string $key, int $default = 0): int
    {
        return (int)$this->get($key, $default);
    }

    /**
     * Returns the parameter value converted to boolean.
     *
     * @return bool The filtered value
     */
    public function getBoolean(string $key, bool $default = false): bool
    {
        return $this->filter($key, $default, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return ArrayIterator An \ArrayIterator instance
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->parameters);
    }

    /**
     * Returns the number of parameters.
     *
     * @return int The number of parameters
     */
    public function count(): int
    {
        return count($this->parameters);
    }

    public function clear(): void
    {
        $this->parameters = [];
    }

}
