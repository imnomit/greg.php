<?php

namespace nomit\Web\Bag;

use Closure;
use nomit\Exception\DeprecatedException;
use nomit\Web\Exception\BadRequestException;
use function is_array;
use function is_object;
use const FILTER_CALLBACK;
use const FILTER_DEFAULT;
use const FILTER_FORCE_ARRAY;
use const FILTER_REQUIRE_ARRAY;

/**
 * InputBag is a container for user input values such as $_GET, $_POST, $_REQUEST, and $_COOKIE.
 *
 * @author Saif Eddin Gmati <saif.gmati@symfony.com>
 */
final class InputBag extends ParameterBag
{

    /**
     * Returns a scalar input value by name.
     *
     * @param string|int|float|bool|null $default The default value if the input key does not exist
     *
     * @return string|int|float|bool|null
     */
    public function get(string $key, $default = null): mixed
    {
        $value = parent::get($key, $this);

        return $this === $value ? $default : $value;
    }

    /**
     * Replaces the current input values by a new set.
     */
    public function replace(array $inputs = []): void
    {
        $this->parameters = [];
        $this->add($inputs);
    }

    /**
     * Adds input values.
     */
    public function add(array $inputs = []): self
    {
        foreach ($inputs as $input => $value) {
            $this->set($input, $value);
        }

        return $this;
    }

    /**
     * Sets an input by name.
     *
     * @param string|int|float|bool|array|null $value
     */
    public function set(string $key, $value): self
    {
        if (null !== $value && !is_scalar($value) && !is_array($value) && !method_exists($value, '__toString')) {
            throw new DeprecatedException(sprintf('Passing "%s" as a 2nd argument to "%s". Pass a scalar, array, or null value instead.', get_debug_type($value), __METHOD__));
        }

        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(string $key, $default = null, int $filter = FILTER_DEFAULT, $options = [])
    {
        $value = $this->has($key) ? $this->all()[$key] : $default;

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        if (is_array($value) && !(($options['flags'] ?? 0) & (FILTER_REQUIRE_ARRAY | FILTER_FORCE_ARRAY))) {
            warning('Filtering an array value with {{method}} without passing the FILTER_REQUIRE_ARRAY or FILTER_FORCE_ARRAY flag is deprecated', ['method' => __METHOD__]);

            if (!isset($options['flags'])) {
                $options['flags'] = FILTER_REQUIRE_ARRAY;
            }
        }

        if ((FILTER_CALLBACK & $filter) && !(($options['options'] ?? null) instanceof Closure)) {
            warning('Not passing a Closure together with FILTER_CALLBACK to {{method}} is deprecated. Wrap your filter in a closure instead.', ['method' => __METHOD__]);
            // throw new \InvalidArgumentException(sprintf('A Closure must be passed to "%s()" when FILTER_CALLBACK is used, "%s" given.', __METHOD__, get_debug_type($options['options'] ?? null)));
        }

        return filter_var($value, $filter, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function all(string $key = null): array
    {
        return parent::all($key);
    }

}
