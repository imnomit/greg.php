<?php

namespace nomit\Kernel\Component;

use nomit\Kernel\Component\AbstractController;
use nomit\Component\ComponentInterface;
use nomit\Exception\InvalidArgumentException;
use nomit\Kernel\Component\ControllerInterface;
use nomit\Kernel\Exception\InvalidLinkException;
use nomit\Kernel\PersistentAttribute;
use nomit\Utility\Reflection;

class ComponentReflection extends \ReflectionClass implements ComponentReflectionInterface
{

    protected static array $persistentParametersCache = [];

    protected static array $persistentComponentsCache = [];

    protected static array $methodCallableCache = [];

    public static function combineArguments(\ReflectionFunctionAbstract $method, array $arguments): array
    {
        $res = [];
        foreach ($method->getParameters() as $i => $param) {
            $name = $param->getName();
            $type = self::getParameterType($param);

            if (isset($arguments[$name])) {
                $res[$i] = $arguments[$name];
                if (!self::convertType($res[$i], $type)) {
                    throw new InvalidArgumentException(sprintf(
                        'Argument $%s passed to %s() must be %s, %s given.',
                        $name,
                        ($method instanceof \ReflectionMethod ? $method->getDeclaringClass()->getName() . '::' : '') . $method->getName(),
                        $type,
                        is_object($arguments[$name]) ? get_class($arguments[$name]) : gettype($arguments[$name]),
                    ));
                }
            } elseif ($param->isDefaultValueAvailable()) {
                $res[$i] = $param->getDefaultValue();
            } elseif ($type === 'scalar' || $param->allowsNull()) {
                $res[$i] = null;
            } elseif ($type === 'array' || $type === 'iterable') {
                $res[$i] = [];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Missing parameter $%s required by %s()',
                    $name,
                    ($method instanceof \ReflectionMethod ? $method->getDeclaringClass()->getName() . '::' : '') . $method->getName(),
                ));
            }
        }

        return $res;
    }

    public static function convertType(&$value, string $types): bool
    {
        foreach (explode('|', $types) as $type) {
            if (self::convertSingleType($value, $type)) {
                return true;
            }
        }

        return false;
    }

    private static function convertSingleType(&$value, string $type): bool
    {
        $builtin = [
            'string' => 1, 'int' => 1, 'float' => 1, 'bool' => 1, 'array' => 1, 'object' => 1,
            'callable' => 1, 'iterable' => 1, 'void' => 1, 'null' => 1, 'mixed' => 1,
            'boolean' => 1, 'integer' => 1, 'double' => 1, 'scalar' => 1,
        ];

        if (empty($builtin[$type])) {
            return $value instanceof $type;

        } elseif ($type === 'object') {
            return is_object($value);

        } elseif ($type === 'callable') {
            return false;

        } elseif ($type === 'scalar') {
            return !is_array($value);

        } elseif ($type === 'array' || $type === 'iterable') {
            return is_array($value);

        } elseif ($type === 'mixed') {
            return true;

        } elseif (!is_scalar($value)) { // array, resource, null, etc.
            return false;

        } else {
            $tmp = ($value === false ? '0' : (string) $value);

            if ($type === 'double' || $type === 'float') {
                $tmp = preg_replace('#\.0*$#D', '', $tmp);
            }

            $orig = $tmp;

            settype($tmp, $type);

            if ($orig !== ($tmp === false ? '0' : (string) $tmp)) {
                return false; // data-loss occurs
            }

            $value = $tmp;
        }

        return true;
    }

    public static function parseAnnotation(\Reflector $reflector, string $name): ?array
    {
        if (!preg_match_all('#[\s*]@' . preg_quote($name, '#') . '(?:\(\s*([^)]*)\s*\)|\s|$)#', (string) $reflector->getDocComment(), $m)) {
            return null;
        }

        $tokens = ['true' => true, 'false' => false, 'null' => null];
        $res = [];
        foreach ($m[1] as $s) {
            foreach (preg_split('#\s*,\s*#', $s, -1, PREG_SPLIT_NO_EMPTY) ?: ['true'] as $item) {
                $res[] = array_key_exists($tmp = strtolower($item), $tokens)
                    ? $tokens[$tmp]
                    : $item;
            }
        }

        return $res;
    }

    public static function getParameterType(\ReflectionParameter $parameter): string
    {
        $default = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
        $type = $parameter->getType();

        return $type
            ? ($type instanceof \ReflectionNamedType ? $type->getName() : (string) $type)
            : ($default === null ? 'scalar' : gettype($default));
    }

    public static function getPropertyType(\ReflectionProperty $property, $default): string
    {
        $type = $property->getType();

        return $type
            ? ($type instanceof \ReflectionNamedType ? $type->getName() : (string) $type)
            : ($default === null ? 'scalar' : gettype($default));
    }

    public static function getClassesAndTraits(string $class): array
    {
        $result = [$class => $class] + class_parents($class);

        $addTraits = function (string $type) use (&$result, &$addTraits): void {
            $result += class_uses($type);
            foreach (class_uses($type) as $trait) {
                $addTraits($trait);
            }
        };

        foreach ($result as $type) {
            $addTraits($type);
        }

        return $result;
    }

    public function getPersistentParameters(?string $class = null): array
    {
        $class ??= $this->getName();
        $params = &self::$persistentParametersCache[$class];

        if ($params !== null) {
            return $params;
        }

        $params = [];

        if (is_subclass_of($class, ComponentInterface::class)) {
            $isController = is_subclass_of($class, ControllerInterface::class);
            $defaults = get_class_vars($class);

            foreach ($defaults as $name => $default) {
                $rp = new \ReflectionProperty($class, $name);

                if (!$rp->isStatic()
                    && ($rp->getAttributes(PersistentAttribute::class)
                        || self::parseAnnotation($rp, 'persistent'))
                ) {
                    $params[$name] = [
                        'def' => $default,
                        'type' => self::getPropertyType($rp, $default),
                        'since' => $isController ? Reflection::getPropertyDeclaringClass($rp)->getName() : null,
                    ];
                }
            }

            foreach ($this->getPersistentParameters(get_parent_class($class)) as $name => $param) {
                if (isset($params[$name])) {
                    $params[$name]['since'] = $param['since'];
                } else {
                    $params[$name] = $param;
                }
            }
        }

        return $params;
    }

    public function getPersistentComponents(?string $class = null): array
    {
        $class ??= $this->getName();
        $components = &self::$persistentComponentsCache[$class];

        if ($components !== null) {
            return $components;
        }

        $components = [];

        if (is_subclass_of($class, ControllerInterface::class)) {
            foreach ($class::getPersistentComponents() as $name => $meta) {
                if (is_string($meta)) {
                    $name = $meta;
                }

                $components[$name] = ['since' => $class];
            }

            $components = $this->getPersistentComponents(get_parent_class($class)) + $components;
        }

        return $components;
    }

    public function saveState(ComponentInterface $component, array $parameters): void
    {
        $tree = self::getClassesAndTraits($component::class);

        foreach ($this->getPersistentParameters() as $name => $meta) {
            if (isset($params[$name])) {
                // injected value

            } elseif (
                array_key_exists($name, $parameters) // nulls are skipped
                || (isset($meta['since']) && !isset($tree[$meta['since']])) // not related
                || !isset($component->$name)
            ) {
                continue;

            } else {
                $params[$name] = $component->$name; // object property value
            }

            if (!self::convertType($parameters[$name], $meta['type'])) {
                throw new InvalidLinkException(sprintf(
                    "Value passed to persistent parameter '%s' in %s must be %s, %s given.",
                    $name,
                    $component instanceof AbstractController ? 'controller ' . $component->getName() : "component '{$component->getUniqueId()}'",
                    $meta['type'],
                    is_object($params[$name]) ? get_class($params[$name]) : gettype($params[$name]),
                ));
            }

            if ($params[$name] === $meta['def'] || ($meta['def'] === null && $params[$name] === '')) {
                $params[$name] = null; // value transmit is unnecessary
            }
        }
    }

    public function hasCallableMethod(string $method): bool
    {
        $class = $this->getName();
        $cache = &self::$methodCallableCache[strtolower($class . ':' . $method)];

        if ($cache === null) {
            try {
                $cache = false;
                $rm = new \ReflectionMethod($class, $method);
                $cache = $this->isInstantiable() && $rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic();
            } catch (\ReflectionException $e) {
            }
        }

        return $cache;
    }

    public function hasAnnotation(string $name): bool
    {
        return (bool) self::parseAnnotation($this, $name);
    }

    public function getAnnotation(string $name): mixed
    {
        $res = self::parseAnnotation($this, $name);

        return $res ? end($res) : null;
    }

    public function getMethod($name): MethodReflection
    {
        return new MethodReflection($this->getName(), $name);
    }

    public function getMethods($filter = -1): array
    {
        foreach ($res = parent::getMethods($filter) as $key => $val) {
            $res[$key] = new MethodReflection($this->getName(), $val->getName());
        }

        return $res;
    }

}