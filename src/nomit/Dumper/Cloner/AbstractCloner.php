<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Dumper\Cloner;

use nomit\Dumper\Caster\Caster;
use nomit\Dumper\Exception\ThrowingCasterException;

/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = [
        '__PHP_Incomplete_Class' => ['nomit\Dumper\Caster\Caster', 'castPhpIncompleteClass'],

        'nomit\Dumper\Caster\CutStub' => ['nomit\Dumper\Caster\StubCaster', 'castStub'],
        'nomit\Dumper\Caster\CutArrayStub' => ['nomit\Dumper\Caster\StubCaster', 'castCutArray'],
        'nomit\Dumper\Caster\ConstStub' => ['nomit\Dumper\Caster\StubCaster', 'castStub'],
        'nomit\Dumper\Caster\EnumStub' => ['nomit\Dumper\Caster\StubCaster', 'castEnum'],

        'Closure' => ['nomit\Dumper\Caster\ReflectionCaster', 'castClosure'],
        'Generator' => ['nomit\Dumper\Caster\ReflectionCaster', 'castGenerator'],
        'ReflectionType' => ['nomit\Dumper\Caster\ReflectionCaster', 'castType'],
        'ReflectionAttribute' => ['nomit\Dumper\Caster\ReflectionCaster', 'castAttribute'],
        'ReflectionGenerator' => ['nomit\Dumper\Caster\ReflectionCaster', 'castReflectionGenerator'],
        'ReflectionClass' => ['nomit\Dumper\Caster\ReflectionCaster', 'castClass'],
        'ReflectionClassConstant' => ['nomit\Dumper\Caster\ReflectionCaster', 'castClassConstant'],
        'ReflectionFunctionAbstract' => ['nomit\Dumper\Caster\ReflectionCaster', 'castFunctionAbstract'],
        'ReflectionMethod' => ['nomit\Dumper\Caster\ReflectionCaster', 'castMethod'],
        'ReflectionParameter' => ['nomit\Dumper\Caster\ReflectionCaster', 'castParameter'],
        'ReflectionProperty' => ['nomit\Dumper\Caster\ReflectionCaster', 'castProperty'],
        'ReflectionReference' => ['nomit\Dumper\Caster\ReflectionCaster', 'castReference'],
        'ReflectionExtension' => ['nomit\Dumper\Caster\ReflectionCaster', 'castExtension'],
        'ReflectionZendExtension' => ['nomit\Dumper\Caster\ReflectionCaster', 'castZendExtension'],

        'Doctrine\Common\Persistence\ObjectManager' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'Doctrine\Common\Proxy\Proxy' => ['nomit\Dumper\Caster\DoctrineCaster', 'castCommonProxy'],
        'Doctrine\ORM\Proxy\Proxy' => ['nomit\Dumper\Caster\DoctrineCaster', 'castOrmProxy'],
        'Doctrine\ORM\PersistentCollection' => ['nomit\Dumper\Caster\DoctrineCaster', 'castPersistentCollection'],
        'Doctrine\Persistence\ObjectManager' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],

        'DOMException' => ['nomit\Dumper\Caster\DOMCaster', 'castException'],
        'DOMStringList' => ['nomit\Dumper\Caster\DOMCaster', 'castLength'],
        'DOMNameList' => ['nomit\Dumper\Caster\DOMCaster', 'castLength'],
        'DOMImplementation' => ['nomit\Dumper\Caster\DOMCaster', 'castImplementation'],
        'DOMImplementationList' => ['nomit\Dumper\Caster\DOMCaster', 'castLength'],
        'DOMNode' => ['nomit\Dumper\Caster\DOMCaster', 'castNode'],
        'DOMNameSpaceNode' => ['nomit\Dumper\Caster\DOMCaster', 'castNameSpaceNode'],
        'DOMDocument' => ['nomit\Dumper\Caster\DOMCaster', 'castDocument'],
        'DOMNodeList' => ['nomit\Dumper\Caster\DOMCaster', 'castLength'],
        'DOMNamedNodeMap' => ['nomit\Dumper\Caster\DOMCaster', 'castLength'],
        'DOMCharacterData' => ['nomit\Dumper\Caster\DOMCaster', 'castCharacterData'],
        'DOMAttr' => ['nomit\Dumper\Caster\DOMCaster', 'castAttr'],
        'DOMElement' => ['nomit\Dumper\Caster\DOMCaster', 'castElement'],
        'DOMText' => ['nomit\Dumper\Caster\DOMCaster', 'castText'],
        'DOMTypeinfo' => ['nomit\Dumper\Caster\DOMCaster', 'castTypeinfo'],
        'DOMDomError' => ['nomit\Dumper\Caster\DOMCaster', 'castDomError'],
        'DOMLocator' => ['nomit\Dumper\Caster\DOMCaster', 'castLocator'],
        'DOMDocumentType' => ['nomit\Dumper\Caster\DOMCaster', 'castDocumentType'],
        'DOMNotation' => ['nomit\Dumper\Caster\DOMCaster', 'castNotation'],
        'DOMEntity' => ['nomit\Dumper\Caster\DOMCaster', 'castEntity'],
        'DOMProcessingInstruction' => ['nomit\Dumper\Caster\DOMCaster', 'castProcessingInstruction'],
        'DOMXPath' => ['nomit\Dumper\Caster\DOMCaster', 'castXPath'],

        'XMLReader' => ['nomit\Dumper\Caster\XmlReaderCaster', 'castXmlReader'],

        'ErrorException' => ['nomit\Dumper\Caster\ExceptionCaster', 'castErrorException'],
        'Exception' => ['nomit\Dumper\Caster\ExceptionCaster', 'castException'],
        'Error' => ['nomit\Dumper\Caster\ExceptionCaster', 'castError'],
        'Symfony\Bridge\Monolog\Logger' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'nomit\DependencyInjector\ContainerInterface' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\EventDispatcher\EventDispatcherInterface' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\HttpClient\CurlHttpClient' => ['nomit\Dumper\Caster\nomit\Caster', 'castHttpClient'],
        'Symfony\Component\HttpClient\NativeHttpClient' => ['nomit\Dumper\Caster\nomit\Caster', 'castHttpClient'],
        'Symfony\Component\HttpClient\Response\CurlResponse' => ['nomit\Dumper\Caster\nomit\Caster', 'castHttpClientResponse'],
        'Symfony\Component\HttpClient\Response\NativeResponse' => ['nomit\Dumper\Caster\nomit\Caster', 'castHttpClientResponse'],
        'nomit\Web\Request' => ['nomit\Dumper\Caster\nomit\Caster', 'castRequest'],
        'nomit\Dumper\Exception\ThrowingCasterException' => ['nomit\Dumper\Caster\ExceptionCaster', 'castThrowingCasterException'],
        'nomit\Dumper\Caster\TraceStub' => ['nomit\Dumper\Caster\ExceptionCaster', 'castTraceStub'],
        'nomit\Dumper\Caster\FrameStub' => ['nomit\Dumper\Caster\ExceptionCaster', 'castFrameStub'],
        'nomit\Dumper\Cloner\AbstractCloner' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'Symfony\Component\ErrorHandler\Exception\SilencedErrorContext' => ['nomit\Dumper\Caster\ExceptionCaster', 'castSilencedErrorContext'],

        'nomit\Image\Image\ImageInterface' => ['nomit\Dumper\Caster\ImagineCaster', 'castImage'],

        'Ramsey\Uuid\UuidInterface' => ['nomit\Dumper\Caster\UuidCaster', 'castRamseyUuid'],

        'ProxyManager\Proxy\ProxyInterface' => ['nomit\Dumper\Caster\ProxyManagerCaster', 'castProxy'],
        'PHPUnit_Framework_MockObject_MockObject' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\MockObject' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\Stub' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'Prophecy\Prophecy\ProphecySubjectInterface' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],
        'Mockery\MockInterface' => ['nomit\Dumper\Caster\StubCaster', 'cutInternals'],

        'PDO' => ['nomit\Dumper\Caster\PdoCaster', 'castPdo'],
        'PDOStatement' => ['nomit\Dumper\Caster\PdoCaster', 'castPdoStatement'],

        'AMQPConnection' => ['nomit\Dumper\Caster\AmqpCaster', 'castConnection'],
        'AMQPChannel' => ['nomit\Dumper\Caster\AmqpCaster', 'castChannel'],
        'AMQPQueue' => ['nomit\Dumper\Caster\AmqpCaster', 'castQueue'],
        'AMQPExchange' => ['nomit\Dumper\Caster\AmqpCaster', 'castExchange'],
        'AMQPEnvelope' => ['nomit\Dumper\Caster\AmqpCaster', 'castEnvelope'],

        'ArrayObject' => ['nomit\Dumper\Caster\SplCaster', 'castArrayObject'],
        'ArrayIterator' => ['nomit\Dumper\Caster\SplCaster', 'castArrayIterator'],
        'SplDoublyLinkedList' => ['nomit\Dumper\Caster\SplCaster', 'castDoublyLinkedList'],
        'SplFileInfo' => ['nomit\Dumper\Caster\SplCaster', 'castFileInfo'],
        'SplFileObject' => ['nomit\Dumper\Caster\SplCaster', 'castFileObject'],
        'SplHeap' => ['nomit\Dumper\Caster\SplCaster', 'castHeap'],
        'SplObjectStorage' => ['nomit\Dumper\Caster\SplCaster', 'castObjectStorage'],
        'SplPriorityQueue' => ['nomit\Dumper\Caster\SplCaster', 'castHeap'],
        'OuterIterator' => ['nomit\Dumper\Caster\SplCaster', 'castOuterIterator'],
        'WeakReference' => ['nomit\Dumper\Caster\SplCaster', 'castWeakReference'],

        'Redis' => ['nomit\Dumper\Caster\RedisCaster', 'castRedis'],
        'RedisArray' => ['nomit\Dumper\Caster\RedisCaster', 'castRedisArray'],
        'RedisCluster' => ['nomit\Dumper\Caster\RedisCaster', 'castRedisCluster'],

        'DateTimeInterface' => ['nomit\Dumper\Caster\DateCaster', 'castDateTime'],
        'DateInterval' => ['nomit\Dumper\Caster\DateCaster', 'castInterval'],
        'DateTimeZone' => ['nomit\Dumper\Caster\DateCaster', 'castTimeZone'],
        'DatePeriod' => ['nomit\Dumper\Caster\DateCaster', 'castPeriod'],

        'GMP' => ['nomit\Dumper\Caster\GmpCaster', 'castGmp'],

        'MessageFormatter' => ['nomit\Dumper\Caster\IntlCaster', 'castMessageFormatter'],
        'NumberFormatter' => ['nomit\Dumper\Caster\IntlCaster', 'castNumberFormatter'],
        'IntlTimeZone' => ['nomit\Dumper\Caster\IntlCaster', 'castIntlTimeZone'],
        'IntlCalendar' => ['nomit\Dumper\Caster\IntlCaster', 'castIntlCalendar'],
        'IntlDateFormatter' => ['nomit\Dumper\Caster\IntlCaster', 'castIntlDateFormatter'],

        'Memcached' => ['nomit\Dumper\Caster\MemcachedCaster', 'castMemcached'],

        'Ds\Collection' => ['nomit\Dumper\Caster\DsCaster', 'castCollection'],
        'Ds\Map' => ['nomit\Dumper\Caster\DsCaster', 'castMap'],
        'Ds\Pair' => ['nomit\Dumper\Caster\DsCaster', 'castPair'],
        'nomit\Dumper\Caster\DsPairStub' => ['nomit\Dumper\Caster\DsCaster', 'castPairStub'],

        'CurlHandle' => ['nomit\Dumper\Caster\ResourceCaster', 'castCurl'],
        ':curl' => ['nomit\Dumper\Caster\ResourceCaster', 'castCurl'],

        ':dba' => ['nomit\Dumper\Caster\ResourceCaster', 'castDba'],
        ':dba persistent' => ['nomit\Dumper\Caster\ResourceCaster', 'castDba'],

        'GdImage' => ['nomit\Dumper\Caster\ResourceCaster', 'castGd'],
        ':gd' => ['nomit\Dumper\Caster\ResourceCaster', 'castGd'],

        ':mysql link' => ['nomit\Dumper\Caster\ResourceCaster', 'castMysqlLink'],
        ':pgsql large object' => ['nomit\Dumper\Caster\PgSqlCaster', 'castLargeObject'],
        ':pgsql link' => ['nomit\Dumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql link persistent' => ['nomit\Dumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql result' => ['nomit\Dumper\Caster\PgSqlCaster', 'castResult'],
        ':process' => ['nomit\Dumper\Caster\ResourceCaster', 'castProcess'],
        ':stream' => ['nomit\Dumper\Caster\ResourceCaster', 'castStream'],

        'OpenSSLCertificate' => ['nomit\Dumper\Caster\ResourceCaster', 'castOpensslX509'],
        ':OpenSSL X.509' => ['nomit\Dumper\Caster\ResourceCaster', 'castOpensslX509'],

        ':persistent stream' => ['nomit\Dumper\Caster\ResourceCaster', 'castStream'],
        ':stream-context' => ['nomit\Dumper\Caster\ResourceCaster', 'castStreamContext'],

        'XmlParser' => ['nomit\Dumper\Caster\XmlResourceCaster', 'castXml'],
        ':xml' => ['nomit\Dumper\Caster\XmlResourceCaster', 'castXml'],

        'RdKafka' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castRdKafka'],
        'RdKafka\Conf' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castConf'],
        'RdKafka\KafkaConsumer' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castKafkaConsumer'],
        'RdKafka\Metadata\Broker' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castBrokerMetadata'],
        'RdKafka\Metadata\Collection' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castCollectionMetadata'],
        'RdKafka\Metadata\Partition' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castPartitionMetadata'],
        'RdKafka\Metadata\Topic' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castTopicMetadata'],
        'RdKafka\Message' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castMessage'],
        'RdKafka\Topic' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castTopic'],
        'RdKafka\TopicPartition' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castTopicPartition'],
        'RdKafka\TopicConf' => ['nomit\Dumper\Caster\RdKafkaCaster', 'castTopicConf'],
    ];

    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;

    private $casters = [];
    private $prevErrorHandler;
    private $classInfo = [];
    private $filter = 0;

    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
    }

    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }

    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }

    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString)
    {
        $this->maxString = $maxString;
    }

    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth)
    {
        $this->minDepth = $minDepth;
    }

    /**
     * Clones a PHP variable.
     *
     * @param mixed $var Any PHP variable
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     *
     * @return Data The cloned variable represented by a Data object
     */
    public function cloneVar($var, int $filter = 0)
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }

            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }

            return false;
        });
        $this->filter = $filter;

        if ($gc = gc_enabled()) {
            gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                gc_enable();
            }
            restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }

    /**
     * Effectively clones the PHP variable.
     *
     * @param mixed $var Any PHP variable
     *
     * @return array The cloned variable represented in an array
     */
    abstract protected function doClone($var);

    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The object casted as array
     */
    protected function castObject(Stub $stub, bool $isNested)
    {
        $obj = $stub->value;
        $class = $stub->class;

        if (\PHP_VERSION_ID < 80000 ? "\0" === ($class[15] ?? null) : false !== strpos($class, "@anonymous\0")) {
            $stub->class = get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = method_exists($class, '__debugInfo');

            foreach (class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';

            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : [
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];

            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }

        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);

        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }

    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The resource casted as array
     */
    protected function castResource(Stub $stub, bool $isNested)
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;

        try {
            if (!empty($this->casters[':' . $type])) {
                foreach ($this->casters[':' . $type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }
}
