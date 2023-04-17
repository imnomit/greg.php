<?php

namespace nomit\Serialization;

use nomit\Dumper\Dumper;
use nomit\Property\Accessor\PropertyAccessor;
use nomit\Serialization\Encoder\EncoderInterface;
use nomit\Serialization\Exception\SerializerException;
use nomit\Serialization\Normalization\Closure\Exception\ExceptionInterface;
use nomit\Serialization\Normalization\Closure\SerializableClosure;
use nomit\Serialization\Normalization\Internal\SplFixedArraySerializer;
use nomit\Serialization\Transformer\TransformerInterface;
use nomit\Utility\Concern\Serializable;
use nomit\Utility\Service\ResetInterface;

class Serializer implements SerializerInterface, ResetInterface
{

    protected static ?string $secretKey = null;

    protected static \SplObjectStorage $objectStorage;

    protected static array $objectMapping = [];

    protected static int $objectMappingIndex = 0;

    private array $dateTimeClassType = ['DateTime', 'DateTimeImmutable', 'DateTimeZone', 'DateInterval', 'DatePeriod'];

    protected array $serializationMap = [
        'array' => 'serializeArray',
        'integer' => 'serializeScalar',
        'double' => 'serializeScalar',
        'boolean' => 'serializeScalar',
        'string' => 'serializeScalar',
    ];

    protected array $unserializationMapHHVM = [];

    public static function setSecretKey(string $key): void
    {
        self::$secretKey = $key;
    }

    public function __construct(
        protected SerializerInterface $encoder
    )
    {
    }

    public function getTransformer(): SerializerInterface
    {
        return $this->encoder;
    }

    public function serialize(mixed $value, array $context = []): string
    {
        $this->reset();

        if(is_object($value) || is_callable($value)) {
            $value = $this->serializeData($value);
        }

        return $this->encoder->serialize($value, $context);
    }

    public function reset()
    {
        self::$objectStorage = new \SplObjectStorage();
        self::$objectMapping = [];
        self::$objectMappingIndex = 0;
    }

    protected function serializeData(mixed $value): mixed
    {
        $this->guardForUnsupportedValues($value);

        if ($this->isInstanceOf($value, 'SplFixedArray')) {
            return SplFixedArraySerializer::serialize($this, $value);
        }

        if (\is_object($value)) {
            return $this->serializeObject($value);
        }

        if($this->isClosure($value)) {
            return $this->serializeClosure($value);
        }

        $type = (\gettype($value) && $value !== null) ? \gettype($value) : 'string';
        $func = $this->serializationMap[$type];

        return $this->$func($value);
    }

    private function isClosure(mixed $value): bool
    {
        if(!is_callable($value)) {
            return false;
        }

        $reflection = new \ReflectionFunction($value);

        return (bool) $reflection->isClosure();
    }

    private function isInstanceOf(mixed $value, string $classFQN): bool
    {
        return is_object($value)
            && (strtolower(get_class($value)) === strtolower($classFQN) || \is_subclass_of($value, $classFQN, true));
    }

    protected function guardForUnsupportedValues(mixed $value): void
    {
        if ($value instanceof \DatePeriod) {
            throw new SerializerException(
                'DatePeriod is not supported in Serializer. Loop through it and serialize the output.'
            );
        }

        if (\is_resource($value)) {
            throw new SerializerException('Resource is not supported in Serializer');
        }
    }

    public function unserialize(mixed $value, array $context = []): mixed
    {
        if (\is_array($value) && isset($value[self::SCALAR_TYPE])) {
            return $this->unserializeData($value);
        }

        $this->reset();

        return $this->unserializeData($this->encoder->unserialize($value, $context));
    }

    protected function unserializeData(mixed $value): mixed
    {
        if ($value === null || !is_array($value)) {
            return $value;
        }

        if (isset($value[self::MAP_TYPE]) && !isset($value[self::CLASS_IDENTIFIER_KEY])) {
            $value = $value[self::SCALAR_VALUE];

            return $this->unserializeData($value);
        }

        if (isset($value[self::SCALAR_TYPE])) {
            return $this->getScalarValue($value);
        }

        if (isset($value[self::CLASS_PARENT_KEY]) && 0 === strcmp($value[self::CLASS_PARENT_KEY], 'SplFixedArray')) {
            return \nomit\Serialization\Normalization\Internal\SplFixedArraySerializer::unserialize($this, $value[self::CLASS_IDENTIFIER_KEY], $value);
        }

        if (isset($value[self::CLASS_IDENTIFIER_KEY])) {
            return $this->unserializeObject($value);
        }

        if(isset($value[self::CLOSURE_IDENTIFIER_KEY])) {
            return $this->unserializeClosure($value);
        }

        return \array_map([$this, __FUNCTION__], $value);
    }

    protected function getScalarValue(mixed $value): string|float|int|null|bool
    {
        switch ($value[self::SCALAR_TYPE]) {
            case 'string':
                return (string) $value[self::SCALAR_VALUE];

            case 'integer':
                return (int) $value[self::SCALAR_VALUE];

            case 'float':
                return (float) $value[self::SCALAR_VALUE];

            case 'boolean':
                return $value[self::SCALAR_VALUE];

            case 'NULL':
                return self::NULL_VAR;
        }

        return $value[self::SCALAR_VALUE];
    }

    protected function unserializeObject(array $value): object
    {
        $className = $value[self::CLASS_IDENTIFIER_KEY];

        unset($value[self::CLASS_IDENTIFIER_KEY]);

        if (isset($value[self::MAP_TYPE])) {
            unset($value[self::MAP_TYPE]);
            unset($value[self::SCALAR_VALUE]);
        }

        if ($className[0] === '@') {
            return self::$objectMapping[substr($className, 1)];
        }

        if (!class_exists($className)) {
            throw new SerializerException('Unable to find class '.$className);
        }

        $obj = $this->unserializeDateTimeFamilyObject($value, $className);

        return (null === ($obj = $this->unserializeDateTimeFamilyObject($value, $className)))
            ? $this->unserializeUserDefinedObject($value, $className) : $obj;
    }

    protected function unserializeDateTimeFamilyObject(array $value, string $className): mixed
    {
        $obj = null;

        if ($this->isDateTimeFamilyObject($className)) {
            $obj = $this->restoreUsingUnserialize($className, $value);

            self::$objectMapping[self::$objectMappingIndex++] = $obj;
        }

        return $obj;
    }

    protected function isDateTimeFamilyObject(string $className): bool
    {
        $isDateTime = false;

        foreach ($this->dateTimeClassType as $class) {
            $isDateTime = $isDateTime || \is_subclass_of($className, $class, true) || $class === $className;
        }

        return $isDateTime;
    }

    protected function restoreUsingUnserialize(string $className, array $attributes): mixed
    {
        foreach ($attributes as &$attribute) {
            $attribute = $this->unserializeData($attribute);
        }

        $object = (object) $attributes;

        $serialized = \preg_replace(
            '|^O:\d+:"\w+":|',
            'O:'.strlen($className).':"'.$className.'":',
            \serialize($object)
        );

        return \unserialize($serialized);
    }

    protected function unserializeUserDefinedObject(array $value, string $className): object
    {
        $ref = new \ReflectionClass($className);
        $obj = $ref->newInstanceWithoutConstructor();

        self::$objectMapping[self::$objectMappingIndex++] = $obj;

        $this->setUnserializedObjectProperties($value, $ref, $obj);

        if($obj instanceof Serializable || method_exists($obj, '__unserialize')) {
            $unserializedValues = [];

            foreach($value as $item) {
                $unserializedValues[] = $this->unserializeData($item);
            }

            $obj->__unserialize($unserializedValues);
        }

        if (\method_exists($obj, '__wakeup')) {
            $obj->__wakeup();
        }

        return $obj;
    }

    protected function setUnserializedObjectProperties(array $value, \ReflectionClass $reflection, object $object): mixed
    {
        foreach ($value as $property => $propertyValue) {
            try {
                $propRef = $reflection->getProperty($property);
                $propRef->setAccessible(true);
                $propRef->setValue($object, $this->unserializeData($propertyValue));
            } catch (\ReflectionException $e) {
                $object->$property = $this->unserializeData($propertyValue);
            }
        }

        return $object;
    }

    protected function unserializeClosure(array $value): \Closure
    {
        $serializedClosure = $value[self::CLOSURE_IDENTIFIER_KEY];

        try {
            $closure = \nomit\Closure\unserialize($serializedClosure);
        } catch(ExceptionInterface $exception) {
            throw new SerializerException(sprintf('An error occurred while attempting to unserialize into an unserializable "%s" object the supplied payload string.', SerializableClosure::class), 500, $exception);
        }

        return $closure->getClosure();
    }

    protected function serializeScalar(mixed $value): array
    {
        $type = \gettype($value);

        if ($type === 'double') {
            $type = 'float';
        }

        return [
            self::SCALAR_TYPE => $type,
            self::SCALAR_VALUE => $value,
        ];
    }

    protected function serializeArray(array $value): array
    {
        if (\array_key_exists(self::MAP_TYPE, $value)) {
            return $value;
        }

        $toArray = [self::MAP_TYPE => 'array', self::SCALAR_VALUE => []];

        foreach ($value as $key => $field) {
            $toArray[self::SCALAR_VALUE][$key] = $this->serializeData($field);
        }

        return $this->serializeData($toArray);
    }

    protected function serializeObject(mixed $value): array
    {
        if (self::$objectStorage->contains($value)) {
            return [self::CLASS_IDENTIFIER_KEY => '@'.self::$objectStorage[$value]];
        }

        self::$objectStorage->attach($value, self::$objectMappingIndex++);

        $reflection = new \ReflectionClass($value);
        $className = $reflection->getName();

        return $this->serializeInternalClass($value, $className, $reflection);
    }

    protected function serializeInternalClass(mixed $value, string $className, \ReflectionClass $reflection): array
    {
        $paramsToSerialize = $this->getObjectProperties($reflection, $value);
        $data = [self::CLASS_IDENTIFIER_KEY => $className];
        $data += \array_map([$this, 'serializeData'], $this->extractObjectData($value, $reflection, $paramsToSerialize));

        return $data;
    }

    protected function getObjectProperties(\ReflectionClass $reflection, mixed $value): array
    {
        $props = [];

        foreach ($reflection->getProperties() as $prop) {
            $props[] = $prop->getName();
        }

        return \array_unique(\array_merge($props, \array_keys(\get_object_vars($value))));
    }

    protected function extractObjectData(mixed $value, \ReflectionClass $reflection, array $properties): array
    {
        $data = [];

        $this->extractCurrentObjectProperties($value, $reflection, $properties, $data);
        $this->extractAllInheritedProperties($value, $reflection, $data);

        return $data;
    }

    protected function extractCurrentObjectProperties(mixed $value, \ReflectionClass $reflection, array $properties, array $data): void
    {
        foreach ($properties as $propertyName) {
            try {
                $propRef = $reflection->getProperty($propertyName);
                $propRef->setAccessible(true);

                $data[$propertyName] = $propRef->getValue($value);
            } catch (\ReflectionException $e) {
                $data[$propertyName] = $value->$propertyName;
            }
        }
    }

    protected function extractAllInheritedProperties(mixed $value, \ReflectionClass $reflection, array &$data): void
    {
        do {
            $rp = array();

            /* @var $property \ReflectionProperty */
            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);
                $rp[$property->getName()] = $property->getValue($value);
            }

            $data = \array_merge($rp, $data);
        } while ($reflection = $reflection->getParentClass());
    }

    protected function serializeClosure(mixed $value): array
    {
        return [
            self::CLOSURE_IDENTIFIER_KEY => \nomit\Closure\serialize($value)
        ];
    }

}