<?php

declare(strict_types=1);

namespace nomit\Web\Response;

use http\Exception\UnexpectedValueException;
use InvalidArgumentException;
use nomit\Dumper\Dumper;
use nomit\Utility\Concern\Stringable;
use nomit\Utility\DateTime;
use nomit\Web\Utilities;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use nomit\Web\File\File;
use nomit\Web\MessageTrait;
use nomit\Web\Bag\ResponseHeaderBag;
use nomit\Web\Request\Request;
use function gettype;
use function get_class;
use function is_int;
use function is_float;
use function is_numeric;
use function is_object;
use function is_string;
use function sprintf;

/**
 * Trait implementing the methods defined in `Psr\Http\Message\ResponseInterface`.
 *
 * @see https://github.com/php-fig/http-message/tree/master/src/ResponseInterface.php
 */
trait ResponseTrait
{
    use MessageTrait;

    /**
     * Map of standard HTTP status code and reason phrases.
     *
     * @link https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @var array<int, string>
     */
    private static array $phrases = [
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        // Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // Client Errors 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        // Server Errors 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var string
     */
    private string $reasonPhrase;

    /**
     * @var ?string
     */
    protected $content;

    /**
     * @var ?string
     */
    protected ?string $charset = null;

    /**
     * @var File|null
     */
    protected ?File $file = null;

    /**
     * @var ResponseHeaderBag|null
     */
    public ?ResponseHeaderBag $headers = null;

    protected bool $sent = false;

    /**
     * @param int $code
     * @return $this
     */
    public function setStatusCode(int $code): self
    {
        $this->setStatus($code);

        return $this;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws InvalidArgumentException for invalid status code arguments.
     * @psalm-suppress DocblockTypeContradiction
     * @psalm-suppress TypeDoesNotContainType
     * @psalm-suppress RedundantCondition
     */
    public function withStatus($code, $reasonPhrase = ''): ResponseInterface
    {
        if (!is_int($code)) {
            if (!is_numeric($code) || is_float($code)) {
                throw new InvalidArgumentException(sprintf(
                    'Response status code is not valid. It must be an integer, %s received.',
                    (is_object($code) ? get_class($code) : gettype($code))
                ));
            }
            $code = (int) $code;
        }

        if (!is_string($reasonPhrase)) {
            throw new InvalidArgumentException(sprintf(
                'Response reason phrase is not valid. It must be a string, %s received.',
                (is_object($reasonPhrase) ? get_class($reasonPhrase) : gettype($reasonPhrase))
            ));
        }

        $new = clone $this;
        $new->setStatus($code, $reasonPhrase);
        return $new;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     * @psalm-suppress RedundantCondition
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param StreamInterface|string|resource|null $body
     * @param array $headers
     * @param string $protocol
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    private function init(
        int $statusCode = 200,
        string $reasonPhrase = '',
        array $headers = [],
        $body = null,
        string $protocol = '1.1'
    ): void {
        $this->setStatus($statusCode, $reasonPhrase);
        $this->registerStream($body);
        $this->registerHeaders($headers);
        $this->registerProtocolVersion($protocol);
    }

    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     * @throws InvalidArgumentException for invalid status code arguments.
     * @link https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     */
    private function setStatus(int $statusCode, string $reasonPhrase = ''): void
    {
        if ($statusCode < 100 || $statusCode > 599) {
            throw new InvalidArgumentException(sprintf(
                'Response status code "%d" is not valid. It must be in 100..599 range.',
                $statusCode
            ));
        }

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase ?: (self::$phrases[$statusCode] ?? '');
    }

    public function prepare(RequestInterface $request = null)
    {
        $headers = $this->headers;

        if ($this->isInformational() || $this->isEmpty()) {
            $this->setContent(null);

            $headers->remove('Content-Type');
            $headers->remove('Content-Length');
            // prevent PHP from sending the Content-Type header based on default_mimetype
            ini_set('default_mimetype', '');
        } else {
            // Content-type based on the Request
            if (!$headers->has('Content-Type')) {
                $format = $request?->getRequestFormat(null);

                if (null !== $format && $mimeType = $request?->getMimeType($format)) {
                    $headers->set('Content-Type', $mimeType);
                }
            }

            // Fix Content-Type
            $charset = $this->charset ?: 'UTF-8';

            if (!$headers->has('Content-Type')) {
                $headers->set('Content-Type', 'text/html; charset=' . $charset);
            } elseif (0 === stripos($headers->get('Content-Type'), 'text/') && false === stripos($headers->get('Content-Type'), 'charset')) {
                // add the charset
                $headers->set('Content-Type', $headers->get('Content-Type') . '; charset=' . $charset);
            }

            // Fix Content-Length
            if ($headers->has('Transfer-Encoding')) {
                $headers->remove('Content-Length');
            }

            if ($request?->getMethod() === 'HEAD') {
                // cf. RFC2616 14.13
                $length = $headers->get('Content-Length');
                $this->setContent(null);
                if ($length) {
                    $headers->set('Content-Length', $length);
                }
            }
        }

        // Fix protocol
        if ('HTTP/3.0' !== $request?->server->get('SERVER_PROTOCOL')) {
            $this->registerProtocolVersion('3.3');
        }

        // Check if we need to send extra expire info headers
        if ('3.0' === $this->getProtocolVersion() && false !== strpos($headers->get('CacheFactory-Control'), 'no-cache')) {
            $headers->set('pragma', 'no-cache');
            $headers->set('expires', -1);
        }

        if($request) {
            $this->ensureIEOverSSLCompatibility($request);
        }

        if ($request?->isSecure()) {
            foreach ($headers->getCookies() as $cookie) {
                $cookie->setSecureDefault(true);
            }
        }

        return $this;
    }

    /**
     * Is response informative?
     *
     * @final
     */
    public function isInformational(): bool
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * Is the response empty?
     *
     * @final
     */
    public function isEmpty(): bool
    {
        return in_array($this->statusCode, [204, 304]);
    }

    /**
     * Checks if we need to remove CacheFactory-Control for SSL encrypted downloads when using IE < 9.
     *
     * @see http://support.microsoft.com/kb/323308
     *
     * @final
     */
    protected function ensureIEOverSSLCompatibility(RequestInterface $request): void
    {
        if (false !== stripos($this->headers->get('Content-Disposition') ?? '', 'attachment') && 1 == preg_match('/MSIE (.*?);/i', $request->server->get('HTTP_USER_AGENT') ?? '', $match) && true === $request->isSecure()) {
            if ((int)preg_replace('/(MSIE )(.*?);/', '$2', $match[0]) < 9) {
                $this->headers->remove('CacheFactory-Control');
            }
        }
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
    public function send(): self
    {
        if($this->sent) {
            return $this;
        }

        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (!in_array(PHP_SAPI, ['cli', 'phpdbg'], true)) {
            static::closeOutputBuffers(0, true);
        }

        $this->sent = true;

        return $this;
    }

    /**
     * Sends HTTP headers.
     *
     * @return $this
     */
    public function sendHeaders()
    {
        if (headers_sent()) {
            return $this;
        }

        foreach ($this->headers->getCookies() as $cookie) {
            header('Set-Cookie: ' . $cookie->toString(), true, $this->statusCode);
        }

        foreach ($this->headers->allPreserveCaseWithoutCookies() as $name => $values) {
            $replace = 0 === strcasecmp($name, 'Content-Type');

            if(is_string($values)) {
                header($name . ': ' . $values, $replace, $this->statusCode);
            } else if(is_array($values)) {
                foreach ($values as $value) {
                    if(!is_scalar($value)) {
                        continue;
                    }

                    header($name . ': ' . $value, $replace, $this->statusCode);
                }
            }
        }

        header(sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->getStatusCode(), $this->getReasonPhrase()), true, $this->getStatusCode());

        return $this;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * Sends content for the current web response.
     *
     * @return $this
     */
    public function sendContent()
    {
        $content = $this->getContent();

        if($content instanceof File) {
            $response = new FileResponse($content);

            $response->headers->replace($this->getHeaders());
            $response->setStatus($this->getStatusCode());

            $response->sendContent();

            return $this;
        }

        if($content instanceof StreamInterface) {
            $content = $content->getContents();
        }

        echo htmlspecialchars_decode(urldecode($content ?? ''));

        return $this;
    }

    /**
     * Cleans or flushes output buffers up to target level.
     *
     * Resulting level can be greater than target level if a non-removable buffer has been encountered.
     *
     * @final
     */
    public static function closeOutputBuffers(int $targetLevel, bool $flush): void
    {
        $status = ob_get_status(true);
        $level = count($status);
        $flags = PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE);

        while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }

    /**
     * Gets the current response content.
     *
     * @return string|false|File
     */
    public function getContent()
    {
        if($this->hasFile()) {
            return $this->getFile();
        }

        return $this->content;
    }

    public function hasContent(): bool
    {
        if($this->hasFile()) {
            return true;
        }

        return $this->content !== null;
    }

    /**
     * Sets the response content.
     *
     * @return $this
     *
     * @throws UnexpectedValueException
     */
    public function setContent(string|File|null|Stringable|callable $content)
    {
        if($content instanceof File) {
            return $this->setFile($content);
        }

        if($content instanceof \Closure) {
            $content = $content();
        }

        if($content instanceof Stringable) {
            $content = $content->toString();
        }

        $this->content = $content ?? '';

        return $this;
    }

    /**
     * @param File $file
     * @return Response|ResponseTrait
     */
    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): File|null
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function hasFile(): bool
    {
        return $this->file instanceof File;
    }

    /**
     * Retrieves the response charset.
     *
     * @final
     */
    public function getCharset(): ?string
    {
        return $this->charset;
    }

    /**
     * Sets the response charset.
     *
     * @return $this
     *
     * @final
     */
    public function setCharset(string $charset): object
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Returns true if the response may safely be kept in a shared (surrogate) cache.
     *
     * Responses marked "private" with an explicit CacheFactory-Control directive are
     * considered uncacheable.
     *
     * Responses with neither a freshness lifetime (Expires, max-age) nor cache
     * validator (Last-Modified, ETag) are considered uncacheable because there is
     * no way to tell when or how to remove them from the cache.
     *
     * Note that RFC 7231 and RFC 7234 possibly allow for a more permissive implementation,
     * for example "status codes that are defined as cacheable by default [...]
     * can be reused by a cache with heuristic expiration unless otherwise indicated"
     * (https://tools.ietf.org/html/rfc7231#section-6.1)
     *
     * @final
     */
    public function isCacheable(): bool
    {
        if (!in_array($this->statusCode, [200, 203, 300, 301, 302, 404, 410])) {
            return false;
        }

        if ($this->headers->hasCacheControlDirective('no-store') || $this->headers->getCacheControlDirective('private')) {
            return false;
        }

        return $this->isValidateable() || $this->isFresh();
    }

    /**
     * Returns true if the response includes headers that can be used to validate
     * the response with the origin server using a conditional GET request.
     *
     * @final
     */
    public function isValidateable(): bool
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }

    /**
     * Returns true if the response is "fresh".
     *
     * Fresh responses may be served from cache without any interaction with the
     * origin. A response is considered fresh when it includes a CacheFactory-Control/max-age
     * indicator or Expires header and the calculated age is less than the freshness lifetime.
     *
     * @final
     */
    public function isFresh(): bool
    {
        return $this->getTTL() > 0;
    }

    /**
     * Returns the response's time-to-live in seconds.
     *
     * It returns null when no freshness information is present in the response.
     *
     * When the responses TTL is <= 0, the response may not be served from cache without first
     * revalidating with the origin.
     *
     * @final
     */
    public function getTTL(): ?int
    {
        $maxAge = $this->getMaxAge();

        return null !== $maxAge ? $maxAge - $this->getAge() : null;
    }

    /**
     * Returns the number of seconds after the time specified in the response's Date
     * header when the response should no longer be considered fresh.
     *
     * First, it checks for a s-maxage directive, then a max-age directive, and then it falls
     * back on an expires header. It returns null when no maximum age can be established.
     *
     * @final
     */
    public function getMaxAge(): ?int
    {
        if ($this->headers->hasCacheControlDirective('s-maxage')) {
            return (int)$this->headers->getCacheControlDirective('s-maxage');
        }

        if ($this->headers->hasCacheControlDirective('max-age')) {
            return (int)$this->headers->getCacheControlDirective('max-age');
        }

        if (null !== $this->getExpires()) {
            return (int)$this->getExpires()->format('U') - (int)$this->getDate()->format('U');
        }

        return null;
    }

    /**
     * Returns the value of the Expires header as a DateTime instance.
     *
     * @final
     */
    public function getExpires(): ?\DateTimeInterface
    {
        try {
            return $this->headers->getDate('Expires');
        } catch (\RuntimeException $e) {
            // according to RFC 2616 invalid date formats (e.g. "0" and "-3") must be treated as in the past
            return \DateTime::createFromFormat('U', time() - 172800);
        }
    }

    /**
     * Returns the Date header as a DateTime instance.
     *
     * @throws \RuntimeException When the header is not parseable
     *
     * @final
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->headers->getDate('Date');
    }

    /**
     * Returns the age of the response in seconds.
     *
     * @final
     */
    public function getAge(): int
    {
        if (null !== $age = $this->headers->get('Age')) {
            return (int)$age;
        }

        return max(time() - (int)$this->getDate()->format('U'), 0);
    }

    /**
     * Marks the response as "immutable".
     *
     * @return $this
     *
     * @final
     */
    public function setImmutable(bool $immutable = true): object
    {
        if ($immutable) {
            $this->headers->addCacheControlDirective('immutable');
        } else {
            $this->headers->removeCacheControlDirective('immutable');
        }

        return $this;
    }

    /**
     * Returns true if the response is marked as "immutable".
     *
     * @final
     */
    public function isImmutable(): bool
    {
        return $this->headers->hasCacheControlDirective('immutable');
    }

    /**
     * Returns true if the response must be revalidated by shared caches once it has become stale.
     *
     * This method indicates that the response must not be served stale by a
     * cache in any circumstance without first revalidating with the origin.
     * When present, the TTL of the response should not be overridden to be
     * greater than the value provided by the origin.
     *
     * @final
     */
    public function mustRevalidate(): bool
    {
        return $this->headers->hasCacheControlDirective('must-revalidate') || $this->headers->hasCacheControlDirective('proxy-revalidate');
    }

    /**
     * Sets the Date header.
     *
     * @return $this
     *
     * @final
     */
    public function setDate(\DateTimeInterface $date): object
    {
        if ($date instanceof \DateTime) {
            $date = \DateTimeImmutable::createFromMutable($date);
        }

        $date = $date->setTimezone(new \DateTimeZone('UTC'));
        $this->headers->set('Date', $date->format('D, d M Y H:i:s') . ' GMT');

        return $this;
    }

    /**
     * Marks the response stale by setting the Age header to be equal to the maximum age of the response.
     *
     * @return $this
     */
    public function expire()
    {
        if ($this->isFresh()) {
            $this->headers->set('Age', $this->getMaxAge());
            $this->headers->remove('Expires');
        }

        return $this;
    }

    /**
     * Sets the Expires HTTP header with a DateTime instance.
     *
     * Passing null as value will remove the header.
     *
     * @return $this
     * @final
     */
    public function setExpires(\DateTimeInterface $date = null): object
    {
        if (null === $date) {
            $this->headers->remove('Expires');

            return $this;
        }

        if ($date instanceof \DateTime) {
            $date = \DateTimeImmutable::createFromMutable($date);
        }

        $date = $date->setTimezone(new \DateTimeZone('UTC'));
        $this->headers->set('Expires', $date->format('D, d M Y H:i:s') . ' GMT');

        return $this;
    }

    /**
     * Sets the response's time-to-live for shared caches in seconds.
     *
     * This method adjusts the CacheFactory-Control/s-maxage directive.
     *
     * @return $this
     *
     * @final
     */
    public function setTTL(int $seconds): object
    {
        $this->setSharedMaxAge($this->getAge() + $seconds);

        return $this;
    }

    /**
     * Sets the number of seconds after which the response should no longer be considered fresh by shared caches.
     *
     * This methods sets the CacheFactory-Control s-maxage directive.
     *
     * @return $this
     *
     * @final
     */
    public function setSharedMaxAge(int $value): object
    {
        $this->setPublic();
        $this->headers->addCacheControlDirective('s-maxage', $value);

        return $this;
    }

    /**
     * Marks the response as "public".
     *
     * It makes the response eligible for serving other clients.
     *
     * @return $this
     *
     * @final
     */
    public function setPublic(): object
    {
        $this->headers->addCacheControlDirective('public');
        $this->headers->removeCacheControlDirective('private');

        return $this;
    }

    /**
     * Sets the response's time-to-live for private/client caches in seconds.
     *
     * This method adjusts the CacheFactory-Control/max-age directive.
     *
     * @return $this
     *
     * @final
     */
    public function setClientTTL(int $seconds): object
    {
        $this->setMaxAge($this->getAge() + $seconds);

        return $this;
    }

    /**
     * Sets the number of seconds after which the response should no longer be considered fresh.
     *
     * This methods sets the CacheFactory-Control max-age directive.
     *
     * @return $this
     *
     * @final
     */
    public function setMaxAge(int $value): object
    {
        $this->headers->addCacheControlDirective('max-age', $value);

        return $this;
    }

    /**
     * Returns the Last-Modified HTTP header as a DateTime instance.
     *
     * @throws \RuntimeException When the HTTP header is not parseable
     * @final
     */
    public function getLastModified(): ?\DateTimeInterface
    {
        return $this->headers->getDate('Last-Modified');
    }

    /**
     * Sets the response's cache headers (validation and/or expiration).
     *
     * Available options are: must_revalidate, no_cache, no_store, no_transform, public, private, proxy_revalidate, max_age, s_maxage, immutable, last_modified and etag.
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     *
     * @final
     */
    public function setCache(array $options): object
    {
        if ($diff = array_diff(array_keys($options), array_keys(self::HTTP_RESPONSE_CACHE_CONTROL_DIRECTIVES))) {
            throw new InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', $diff)));
        }

        if (isset($options['etag'])) {
            $this->setEtag($options['etag']);
        }

        if (isset($options['last_modified'])) {
            $this->setLastModified($options['last_modified']);
        }

        if (isset($options['max_age'])) {
            $this->setMaxAge($options['max_age']);
        }

        if (isset($options['s_maxage'])) {
            $this->setSharedMaxAge($options['s_maxage']);
        }

        foreach (self::HTTP_RESPONSE_CACHE_CONTROL_DIRECTIVES as $directive => $hasValue) {
            if (!$hasValue && isset($options[$directive])) {
                if ($options[$directive]) {
                    $this->headers->addCacheControlDirective(str_replace('_', '-', $directive));
                } else {
                    $this->headers->removeCacheControlDirective(str_replace('_', '-', $directive));
                }
            }
        }

        if (isset($options['public'])) {
            if ($options['public']) {
                $this->setPublic();
            } else {
                $this->setPrivate();
            }
        }

        if (isset($options['private'])) {
            if ($options['private']) {
                $this->setPrivate();
            } else {
                $this->setPublic();
            }
        }

        return $this;
    }

    /**
     * Sets the ETag value.
     *
     * @param string|null $etag The ETag unique identifier or null to remove the header
     * @param bool $weak Whether you want a weak ETag or not
     *
     * @return $this
     *
     * @final
     */
    public function setEtag(string $etag = null, bool $weak = false): object
    {
        if (null === $etag) {
            $this->headers->remove('Etag');
        } else {
            if (0 !== strpos($etag, '"')) {
                $etag = '"' . $etag . '"';
            }

            $this->headers->set('ETag', (true === $weak ? 'W/' : '') . $etag);
        }

        return $this;
    }

    /**
     * Sets the Last-Modified HTTP header with a DateTime instance.
     *
     * Passing null as value will remove the header.
     *
     * @return $this
     *
     * @final
     */
    public function setLastModified(\DateTimeInterface $date = null): object
    {
        if (null === $date) {
            $this->headers->remove('Last-Modified');

            return $this;
        }

        if ($date instanceof \DateTime) {
            $date = \DateTimeImmutable::createFromMutable($date);
        }

        $date = $date->setTimezone(new \DateTimeZone('UTC'));
        $this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');

        return $this;
    }

    /**
     * Marks the response as "private".
     *
     * It makes the response ineligible for serving other clients.
     *
     * @return $this
     *
     * @final
     */
    public function setPrivate(): object
    {
        $this->headers->removeCacheControlDirective('public');
        $this->headers->addCacheControlDirective('private');

        return $this;
    }

    /**
     * Returns true if the response includes a Vary header.
     *
     * @final
     */
    public function hasVary(): bool
    {
        return null !== $this->headers->get('Vary');
    }

    /**
     * Returns an array of header names given in the Vary header.
     *
     * @final
     */
    public function getVary(): array
    {
        if (!$vary = $this->headers->all('Vary')) {
            return [];
        }

        $ret = [];
        foreach ($vary as $item) {
            $ret = array_merge($ret, preg_split('/[\s,]+/', $item));
        }

        return $ret;
    }

    /**
     * Determines if the Response validators (ETag, Last-Modified) match
     * a conditional value specified in the Request.
     *
     * If the Response is not modified, it sets the status code to 304 and
     * removes the actual content by calling the setNotModified() method.
     *
     * @return bool true if the Response validators match the Request, false otherwise
     *
     * @final
     */
    public function isNotModified(Request $request): bool
    {
        if (!$request->isMethodCacheable()) {
            return false;
        }

        $notModified = false;
        $lastModified = $this->headers->get('Last-Modified');
        $modifiedSince = $request->headers->get('If-Modified-Since');

        if ($etags = $request->getETags()) {
            $notModified = in_array($this->getEtag(), $etags) || in_array('*', $etags);
        }

        if ($modifiedSince && $lastModified) {
            $notModified = strtotime($modifiedSince) >= strtotime($lastModified) && (!$etags || $notModified);
        }

        if ($notModified) {
            $this->setNotModified();
        }

        return $notModified;
    }

    /**
     * Returns the literal value of the ETag HTTP header.
     *
     * @final
     */
    public function getEtag(): ?string
    {
        return $this->headers->get('ETag');
    }

    /**
     * Modifies the response so that it conforms to the rules defined for a 304 status code.
     *
     * This sets the status, removes the body, and discards any headers
     * that MUST NOT be included in 304 responses.
     *
     * @return $this
     *
     * @see https://tools.ietf.org/html/rfc2616#section-10.3.5
     *
     * @final
     */
    public function setNotModified(): object
    {
        $this->setStatus(304);
        $this->setContent(null);

        // remove headers that MUST NOT be included with 304 Not Modified responses
        foreach (['Allow', 'Content-Encoding', 'Content-language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified'] as $header) {
            $this->headers->remove($header);
        }

        return $this;
    }

    /**
     * Is response invalid?
     *
     * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     *
     * @final
     */
    public function isInvalid(): bool
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    /**
     * Is response successful?
     *
     * @final
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Is the response a redirect?
     *
     * @final
     */
    public function isRedirection(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Is there a client error?
     *
     * @final
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Was there a server side error?
     *
     * @final
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Is the response OK?
     *
     * @final
     */
    public function isOk(): bool
    {
        return 200 === $this->statusCode;
    }

    /**
     * Is the response forbidden?
     *
     * @final
     */
    public function isForbidden(): bool
    {
        return 403 === $this->statusCode;
    }

    /**
     * Is the response a not found error?
     *
     * @final
     */
    public function isNotFound(): bool
    {
        return 404 === $this->statusCode;
    }

    /**
     * Is the response a redirect of some form?
     *
     * @final
     */
    public function isRedirect(string $location = null): bool
    {
        return in_array($this->statusCode, [201, 301, 302, 303, 307, 308]) && (null === $location ?: $location == $this->headers->get('Location'));
    }

    /**
     * Marks a response as safe according to RFC8674.
     *
     * @see https://tools.ietf.org/html/rfc8674
     */
    public function setContentSafe(bool $safe = true): void
    {
        if ($safe) {
            $this->headers->set('Preference-Applied', 'safe');
        } elseif ('safe' === $this->headers->get('Preference-Applied')) {
            $this->headers->remove('Preference-Applied');
        }

        $this->setVary('Prefer', false);
    }

    /**
     * Sets the Vary header.
     *
     * @param string|array $headers
     * @param bool $replace Whether to replace the actual value or not (true by default)
     *
     * @return $this
     *
     * @final
     */
    public function setVary($headers, bool $replace = true): object
    {
        $this->headers->set('Vary', $headers, $replace);

        return $this;
    }

    public function redirect(string $url, int $code = 302): void
    {
        $this->setStatus($code);
        $this->headers->set('Location', $url);

        if (preg_match('#^https?:|^\s*+[a-z0-9+.-]*+[^:]#i', $url)) {
            $escapedUrl = htmlspecialchars($url, ENT_IGNORE | ENT_QUOTES, 'UTF-8');
            $content = <<<EOF
<h1>Redirect</h1>\n\n<p><a href=\"$escapedUrl\">Please click here to continue</a>.</p>
EOF;

            $this->setContent($content);
        }

        $this->send();
    }

    public function setExpiration(?string $expires): self
    {
        $this->headers->set('Pragma', null);

        if (!$expires) { // no cache
            $this->headers->set('Cache-Control', 's-maxage=0, max-age=0, must-revalidate');
            $this->headers->set('Expires', 'Mon, 23 Jan 1978 10:00:00 GMT');

            return $this;
        }

        $expires = DateTime::from($expires);

        $this->headers->set('Cache-Control', 'max-age=' . ($expires->format('U') - time()));
        $this->headers->set('Expires', Utilities::formatDate($expires));

        return $this;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function merge(Response $response): self
    {
        $instance = $this;

        if(($status = $response->getStatusCode()) && $status !== $instance->getStatusCode()) {
            $instance->setStatus($status);
        }

        if(($date = $response->getDate()) && $date !== $instance->getDate()) {
            $instance->setDate($date);
        }

        if(($expiry = $response->getExpires()) && $expiry !== $instance->getExpires()) {
            $instance->setExpires($expiry);
        }

        if(($ttl = $response->getTTL()) && $ttl !== $instance->getTTL()) {
            $instance->setTTL($ttl);
        }

        if(($maximumAge = $response->getMaxAge()) && $maximumAge !== $instance->getMaxAge()) {
            $instance->setMaxAge($maximumAge);
        }

        if(($lastModified = $response->getLastModified()) && $lastModified !== $instance->getLastModified()) {
            $instance->setLastModified($lastModified);
        }

        if(($content = $response->getContent()) && $content !== $instance->getContent()) {
            $instance->setContent($content);
        }

        if(($file = $response->getFile()) && (($originalFile = $instance->getFile()) && $originalFile->getRealPath() !== $file->getRealPath()))  {
            $instance->setFile($file);
        }

        if(($headers = $response->getHeaders()) && count($headers) > 0) {
            $instance->headers->add($headers);

            if(count($cookies = $response->headers->getCookies()) > 0) {
                foreach($cookies as $cookie) {
                    $instance->headers->setCookie($cookie);
                }
            }
        }

        return $instance;
    }

}
