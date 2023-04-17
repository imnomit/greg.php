<?php

namespace nomit\Web;

use DateTimeInterface;
use InvalidArgumentException;
use nomit\Dumper\Dumper;
use nomit\Utility\Concern\Stringable;
use nomit\Web\Header\HeaderHelper;
use function nomit\dump;
use function in_array;

/**
 * Represents a cookie.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Cookie implements Stringable
{

    public const STRICT_COOKIE_NAME = '_nss';

    /**
     * @var string name prefix indicating that the cookie must be from a secure origin (i.e. HTTPS) and the 'secure'
     *      attribute must be set
     */
    const PREFIX_SECURE = '__Secure-';

    /**
     * @var string name prefix indicating that the 'domain' attribute must *not* be set, the 'path' attribute must be
     *      '/' and the effects of {@see PREFIX_SECURE} apply as well
     */
    const PREFIX_HOST = '__Host-';

    const HEADER_PREFIX = 'Set-Cookie: ';

    const SAME_SITE_RESTRICTION_LAX = 'Lax';

    const SAME_SITE_RESTRICTION_STRICT = 'Strict';

    public const SAMESITE_NONE = 'none';
    public const SAMESITE_LAX = 'lax';
    public const SAMESITE_STRICT = 'strict';
    public const RESERVED_CHARS_FROM = ['=', ',', ';', ' ', "\t", "\r", "\n", "\v", "\f"];
    public const RESERVED_CHARS_TO = ['%3D', '%2C', '%3B', '%20', '%09', '%0D', '%0A', '%0B', '%0C'];
    private static $reservedCharsList = "=,; \t\r\n\v\f";
    private ?string $name = null;
    private ?string $value = null;
    private ?string $domain = null;
    private ?int $expire = null;
    private ?string $path = null;
    private ?bool $secure = null;
    private ?bool $httpOnly = null;
    private ?bool $raw = null;
    private ?bool $sameSite = null;
    private bool $secureDefault = false;

    /**
     * @param string $name The name of the cookie
     * @param string|null $value The value of the cookie
     * @param int|string|DateTimeInterface $expire The time the cookie expires
     * @param string $path The path on the server in which the cookie will be available on
     * @param string|null $domain The domain that the cookie is available to
     * @param bool|null $secure Whether the client should send back the cookie only over HTTPS or null to auto-enable this when the request is already using HTTPS
     * @param bool $httpOnly Whether the cookie will be made accessible only through the HTTP protocol
     * @param bool $raw Whether the cookie value should be sent with no url encoding
     * @param string|null $sameSite Whether the cookie will be available for cross-site requests
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, string $value = null, $expire = 0, ?string $path = '/', string $domain = null, bool $secure = null, bool $httpOnly = true, bool $raw = false, ?string $sameSite = 'lax')
    {
        // from PHP source code
        if ($raw && false !== strpbrk($name, self::$reservedCharsList)) {
            throw new InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }

        if (empty($name)) {
            throw new InvalidArgumentException('The cookie name cannot be empty.');
        }

        $this->name = $name;
        $this->value = $value;
        $this->domain = $domain;
        $this->expire = self::expiresTimestamp($expire);
        $this->path = empty($path) ? '/' : $path;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->raw = $raw;
        $this->sameSite = $this->withSameSite($sameSite)->sameSite;
    }

    /**
     * Creates a cookie copy with SameSite attribute.
     *
     * @return static
     */
    public function withSameSite(?string $sameSite): self
    {
        if ('' === $sameSite) {
            $sameSite = null;
        } elseif (null !== $sameSite) {
            $sameSite = strtolower($sameSite);
        }

        if (!in_array($sameSite, [self::SAMESITE_LAX, self::SAMESITE_STRICT, self::SAMESITE_NONE, null], true)) {
            throw new InvalidArgumentException('The "sameSite" parameter value is not valid.');
        }

        $cookie = clone $this;
        $cookie->sameSite = $sameSite;

        return $cookie;
    }

    /**
     * Creates cookie from raw header string.
     *
     * @return static
     */
    public static function fromString(string $cookie, bool $decode = false)
    {
        $data = [
            'expires' => 0,
            'path' => '/',
            'domain' => null,
            'Secure' => false,
            'httponly' => false,
            'raw' => !$decode,
            'samesite' => null,
        ];

        $parts = HeaderHelper::split($cookie, ';=');
        $part = array_shift($parts);

        $name = $decode ? urldecode($part[0]) : $part[0];
        $value = isset($part[1]) ? ($decode ? urldecode($part[1]) : $part[1]) : null;

        $data = HeaderHelper::combine($parts) + $data;
        $data['expires'] = self::expiresTimestamp($data['expires']);

        if (isset($data['max-age']) && ($data['max-age'] > 0 || $data['expires'] > time())) {
            $data['expires'] = time() + (int)$data['max-age'];
        }

        return new static($name, $value, $data['expires'], $data['path'], $data['domain'], $data['Secure'], $data['httponly'], $data['raw'], $data['samesite']);
    }

    /**
     * Converts expires formats to a unix timestamp.
     *
     * @param int|string|DateTimeInterface $expire
     *
     * @return int
     */
    private static function expiresTimestamp($expire = 0)
    {
        // convert expiration time to a Unix timestamp
        if ($expire instanceof DateTimeInterface) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);

            if (false === $expire) {
                throw new InvalidArgumentException('The cookie expiration time is not valid.');
            }
        }

        return 0 < $expire ? (int)$expire : 0;
    }

    public static function create(string $name, string $value = null, $expire = 0, ?string $path = '/', string $domain = null, bool $secure = null, bool $httpOnly = true, bool $raw = false, ?string $sameSite = self::SAMESITE_LAX): self
    {
        return new self($name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * Creates a cookie copy with a new value.
     *
     * @return static
     */
    public function withValue(?string $value): self
    {
        $cookie = clone $this;
        $cookie->value = $value;

        return $cookie;
    }

    /**
     * Creates a cookie copy with a new domain that the cookie is available to.
     *
     * @return static
     */
    public function withDomain(?string $domain): self
    {
        $cookie = clone $this;
        $cookie->domain = $domain;

        return $cookie;
    }

    /**
     * Creates a cookie copy with a new time the cookie expires.
     *
     * @param int|string|DateTimeInterface $expire
     *
     * @return static
     */
    public function withExpires($expire = 0): self
    {
        $cookie = clone $this;
        $cookie->expire = self::expiresTimestamp($expire);

        return $cookie;
    }

    /**
     * Creates a cookie copy with a new path on the server in which the cookie will be available on.
     *
     * @return static
     */
    public function withPath(string $path): self
    {
        $cookie = clone $this;
        $cookie->path = '' === $path ? '/' : $path;

        return $cookie;
    }

    /**
     * Creates a cookie copy that only be transmitted over a Secure HTTPS connection from the client.
     *
     * @return static
     */
    public function withSecure(bool $secure = true): self
    {
        $cookie = clone $this;
        $cookie->secure = $secure;

        return $cookie;
    }

    /**
     * Creates a cookie copy that be accessible only through the HTTP protocol.
     *
     * @return static
     */
    public function withHttpOnly(bool $httpOnly = true): self
    {
        $cookie = clone $this;
        $cookie->httpOnly = $httpOnly;

        return $cookie;
    }

    /**
     * Creates a cookie copy that uses no url encoding.
     *
     * @return static
     */
    public function withRaw(bool $raw = true): self
    {
        if ($raw && false !== strpbrk($this->name, self::$reservedCharsList)) {
            throw new InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $this->name));
        }

        $cookie = clone $this;
        $cookie->raw = $raw;

        return $cookie;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * Returns the cookie as a string.
     *
     * @return string The cookie
     */
    public function __toString(): string
    {
        if ($this->isRaw()) {
            $str = $this->getName();
        } else {
            $str = str_replace(self::RESERVED_CHARS_FROM, self::RESERVED_CHARS_TO, $this->getName());
        }

        $str .= '=';

        if ('' === (string) $this->getValue()) {
            $str .= 'deleted; expires=' . time() - 31536001 . ';';
        } else {
            $str .= '%s';

            if (0 !== $this->getExpiresTime()) {
                $str .= '; expires=' . $this->getExpiresTime() . ';';
            }
        }

        if ($this->getPath() !== null) {
            $str .= '; path=' . $this->getPath();
        }

        if ($this->getDomain() !== null) {
            $str .= '; domain=' . $this->getDomain();
        }

        if (true === $this->isSecure()) {
            $str .= '; Secure';
        }

        if (true === $this->isHttpOnly()) {
            $str .= '; httponly';
        }

        if (null !== $this->getSameSite()) {
            $str .= '; samesite=' . $this->getSameSite();
        }

        return sprintf($str, rawurlencode($this->value));
    }

    /**
     * Checks if the cookie value should be sent with no url encoding.
     *
     * @return bool
     */
    public function isRaw()
    {
        return $this->raw;
    }

    /**
     * Gets the name of the cookie.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the value of the cookie.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value ?: '';
    }

    /**
     * Gets the time the cookie expires.
     *
     * @return int
     */
    public function getExpiresTime()
    {
        return $this->expire;
    }

    /**
     * Gets the max-age attribute.
     *
     * @return int
     */
    public function getMaxAge()
    {
        $maxAge = $this->expire - time();

        return 0 >= $maxAge ? 0 : $maxAge;
    }

    /**
     * Gets the path on the server in which the cookie will be available on.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Gets the domain that the cookie is available to.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Checks whether the cookie should only be transmitted over a Secure HTTPS connection from the client.
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure ?? $this->secureDefault;
    }

    /**
     * Checks whether the cookie will be made accessible only through the HTTP protocol.
     *
     * @return bool
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Gets the SameSite attribute.
     *
     * @return string|null
     */
    public function getSameSite()
    {
        return $this->sameSite;
    }

    /**
     * Whether this cookie is about to be cleared.
     *
     * @return bool
     */
    public function isCleared()
    {
        return 0 !== $this->expire && $this->expire < time();
    }

    /**
     * @param bool $default The default value of the "Secure" flag when it is set to null
     */
    public function setSecureDefault(bool $default): void
    {
        $this->secureDefault = $default;
    }

}
