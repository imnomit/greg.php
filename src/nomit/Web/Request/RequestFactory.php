<?php

declare(strict_types=1);

namespace nomit\Web\Request;

use nomit\Dumper\Dumper;
use nomit\Exception\InvalidStateException;
use nomit\Utility\String\Strings;
use nomit\Web\Url\Url;
use nomit\Web\Url\UrlInterface;
use nomit\Web\Url\UrlScript;
use nomit\Web\Utilities;
use Psr\Http\Message\RequestFactoryInterface;
use nomit\Web\Request\RequestInterface;

final class RequestFactory implements RequestFactoryInterface
{

    /** @internal */
    private const ValidChars = '\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}';

    public array $urlFilters = [
        'path' => ['#//#' => '/'], // '%20' => ''
        'url' => [], // '#[.,)]$#D' => ''
    ];

    private bool $binary = false;

    /** @var string[] */
    private array $proxies = [];

    public function setBinary(bool $binary = true): static
    {
        $this->binary = $binary;
        return $this;
    }

    /**
     * @param  string|string[]  $proxy
     */
    public function setProxy($proxy): static
    {
        $this->proxies = (array) $proxy;
        return $this;
    }


    /**
     * Returns new Request instance, using values from superglobals.
     */
    public static function createFromGlobals(string $method = null, $uri = null): RequestInterface
    {
        $instance = new self;

        return $instance->create($method, $uri);
    }

    public function create(string $method = null, $uri = null): RequestInterface
    {
        $uri = $uri ?? $_SERVER['REQUEST_URI'] ?? '';
        $url = new Url($uri);

        $this->getServer($url);
        $this->getPathAndQuery($url);

        [$post, $cookies] = $this->getGetPostCookie($url);

        return Request::create(
            $uri,
            $method ?? $this->getMethod(),
            $post,
            $cookies,
            $this->getFiles(),
            $this->getServerParameters(),
            'php://input'
        );
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->createFromGlobals($method, $uri);
    }

    private function getServer(Url $url): void
    {
        $url->setScheme(!empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off') ? 'https' : 'http');

        if (
            (isset($_SERVER[$tmp = 'HTTP_HOST']) || isset($_SERVER[$tmp = 'SERVER_NAME']))
            && preg_match('#^([a-z0-9_.-]+|\[[a-f0-9:]+\])(:\d+)?$#Di', $_SERVER[$tmp], $pair)
        ) {
            $url->setHost(rtrim(strtolower($pair[1]), '.'));

            if (isset($pair[2])) {
                $url->setPort((int) substr($pair[2], 1));
            } elseif (isset($_SERVER['SERVER_PORT'])) {
                $url->setPort((int) $_SERVER['SERVER_PORT']);
            }
        }
    }


    private function getPathAndQuery(Url $url): void
    {
        $requestUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $requestUrl = preg_replace('#^\w++://[^/]++#', '', $requestUrl);
        $requestUrl = Strings::replace($requestUrl, $this->urlFilters['url']);

        $tmp = explode('?', $requestUrl, 2);
        $path = Url::unescape($tmp[0], '%/?#');
        $path = Strings::fixEncoding(Strings::replace($path, $this->urlFilters['path']));

        $url->setPath($path);
        $url->setQuery($tmp[1] ?? '');
    }


    private function getScriptPath(UrlInterface $url): string
    {
        if (PHP_SAPI === 'cli-server') {
            return '/';
        }

        $path = $url->getPath();
        $lpath = strtolower($path);
        $script = strtolower($_SERVER['SCRIPT_NAME'] ?? '');

        if ($lpath !== $script) {
            $max = min(strlen($lpath), strlen($script));
            for ($i = 0; $i < $max && $lpath[$i] === $script[$i]; $i++);
            $path = $i
                ? substr($path, 0, strrpos($path, '/', $i - strlen($path) - 1) + 1)
                : '/';
        }

        return $path;
    }


    private function getGetPostCookie(UrlInterface $url): array
    {
        $useFilter = (!in_array(ini_get('filter.default'), ['', 'unsafe_raw'], true) || ini_get('filter.default_flags'));

        $query = $url->getQueryParameters();
        $post = $useFilter
            ? filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW)
            : (empty($_POST) ? [] : $_POST);
        $cookies = $useFilter
            ? filter_input_array(INPUT_COOKIE, FILTER_UNSAFE_RAW)
            : (empty($_COOKIE) ? [] : $_COOKIE);

        // remove invalid characters
        $reChars = '#^[' . self::ValidChars . ']*+$#Du';

        if (!$this->binary) {
            $list = [&$query, &$post, &$cookies];
            foreach ($list as $key => &$val) {
                foreach ($val as $k => $v) {
                    if (is_string($k) && (!preg_match($reChars, $k) || preg_last_error())) {
                        unset($list[$key][$k]);

                    } elseif (is_array($v)) {
                        $list[$key][$k] = $v;
                        $list[] = &$list[$key][$k];

                    } elseif (is_string($v)) {
                        $list[$key][$k] = (string) preg_replace('#[^' . self::ValidChars . ']+#u', '', $v);

                    } else {
                        throw new InvalidStateException(sprintf('Invalid value in $_POST/$_COOKIE in key %s, expected string, %s given.', "'$k'", gettype($v)));
                    }
                }
            }

            unset($list, $key, $val, $k, $v);
        }

        $url->setQuery($query);

        return [$post, $cookies];
    }


    private function getFiles(): array
    {
        return $_FILES;
    }


    private function getServerParameters(): array
    {
        $headers = [];

        foreach ($_SERVER as $k => $v) {
            $headers[str_replace('_', '-', $k)] = $v;
        }
        
        return $headers;
    }


    private function getMethod(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (
            $method === 'POST'
            && preg_match('#^[A-Z]+$#D', $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? '')
        ) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }

        return $method;
    }


    private function getClient(UrlInterface $url): array
    {
        $remoteAddr = !empty($_SERVER['REMOTE_ADDR'])
            ? $_SERVER['REMOTE_ADDR']
            : null;
        $remoteHost = !empty($_SERVER['REMOTE_HOST'])
            ? $_SERVER['REMOTE_HOST']
            : null;

        // use real client address and host if trusted proxy is used
        $usingTrustedProxy = $remoteAddr && array_filter($this->proxies, fn(string $proxy): bool => Utilities::ipMatch($remoteAddr, $proxy));
        if ($usingTrustedProxy) {
            empty($_SERVER['HTTP_FORWARDED'])
                ? $this->useNonstandardProxy($url, $remoteAddr, $remoteHost)
                : $this->useForwardedProxy($url, $remoteAddr, $remoteHost);
        }

        return [$remoteAddr, $remoteHost];
    }


    private function getUserAndPassword(): array
    {
        $user = $_SERVER['PHP_AUTH_USER'] ?? null;
        $password = $_SERVER['PHP_AUTH_PW'] ?? null;

        return [$user, $password];
    }


    private function useForwardedProxy(Url $url, &$remoteAddr, &$remoteHost): void
    {
        $forwardParams = preg_split('/[,;]/', $_SERVER['HTTP_FORWARDED']);
        foreach ($forwardParams as $forwardParam) {
            [$key, $value] = explode('=', $forwardParam, 2) + [1 => ''];
            $proxyParams[strtolower(trim($key))][] = trim($value, " \t\"");
        }

        if (isset($proxyParams['for'])) {
            $address = $proxyParams['for'][0];
            $remoteAddr = str_contains($address, '[')
                ? substr($address, 1, strpos($address, ']') - 1) // IPv6
                : explode(':', $address)[0];  // IPv4
        }

        if (isset($proxyParams['host']) && count($proxyParams['host']) === 1) {
            $host = $proxyParams['host'][0];
            $startingDelimiterPosition = strpos($host, '[');
            if ($startingDelimiterPosition === false) { //IPv4
                $remoteHostArr = explode(':', $host);
                $remoteHost = $remoteHostArr[0];
                $url->setHost($remoteHost);
                if (isset($remoteHostArr[1])) {
                    $url->setPort((int) $remoteHostArr[1]);
                }
            } else { //IPv6
                $endingDelimiterPosition = strpos($host, ']');
                $remoteHost = substr($host, strpos($host, '[') + 1, $endingDelimiterPosition - 1);
                $url->setHost($remoteHost);
                $remoteHostArr = explode(':', substr($host, $endingDelimiterPosition));
                if (isset($remoteHostArr[1])) {
                    $url->setPort((int) $remoteHostArr[1]);
                }
            }
        }

        $scheme = (isset($proxyParams['proto']) && count($proxyParams['proto']) === 1)
            ? $proxyParams['proto'][0]
            : 'http';
        $url->setScheme(strcasecmp($scheme, 'https') === 0 ? 'https' : 'http');
    }


    private function useNonstandardProxy(Url $url, &$remoteAddr, &$remoteHost): void
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $url->setScheme(strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0 ? 'https' : 'http');
            $url->setPort($url->getScheme() === 'https' ? 443 : 80);
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_PORT'])) {
            $url->setPort((int)$_SERVER['HTTP_X_FORWARDED_PORT']);
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $xForwardedForWithoutProxies = array_filter(
                explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']),
                fn(string $ip): bool => !array_filter(
                    $this->proxies,
                    fn(string $proxy): bool => filter_var(trim($ip), FILTER_VALIDATE_IP) !== false && Helpers::ipMatch(trim($ip), $proxy),
                ),
            );
            if ($xForwardedForWithoutProxies) {
                $remoteAddr = trim(end($xForwardedForWithoutProxies));
                $xForwardedForRealIpKey = key($xForwardedForWithoutProxies);
            }
        }

        if (isset($xForwardedForRealIpKey) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $xForwardedHost = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
            if (isset($xForwardedHost[$xForwardedForRealIpKey])) {
                $remoteHost = trim($xForwardedHost[$xForwardedForRealIpKey]);
                $url->setHost($remoteHost);
            }
        }
    }

}
