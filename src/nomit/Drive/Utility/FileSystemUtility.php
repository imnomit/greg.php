<?php

namespace nomit\Drive\Utility;

use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Bitmask\BitmaskUtility;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Enumeration\EnumerationInterface;

final class FileSystemUtility
{

    private static $resource;

    public static function normalizePathname(string $pathname): string
    {
        $pathname = str_replace('\\', '/', $pathname);

        preg_match('@^((?>[a-zA-Z]:)?/)?@', $pathname, $match);

        if (empty($match[1])) {
            $fragment = '';
        }  else {
            $fragment  = $match[1];
            $pathname = substr($pathname, strlen($fragment));
        }

        $pathname  = preg_replace('@^[/\s]+|[/\s]+$@', '', $pathname);
        $pathname  = preg_replace('@/+@', '/', $pathname);
        $parts = [];

        foreach (explode('/', $pathname) as $part) {
            if ($part === '.' || $part === '..' && array_pop($parts) || $part === $fragment) {
                continue;
            }

            $parts[] = $part;
        }

        return $fragment . implode('/', $parts);
    }

    public static function getAbsolutePathnamePrefix(string $pathname): string
    {
        preg_match('|^(?P<prefix>([a-zA-Z]:)?/)|', $pathname, $matches);

        if (empty($matches['prefix'])) {
            return '';
        }

        return strtolower($matches['prefix']);
    }

    public static function stripPatternFromPathname(string $pattern, string $path): string
    {
        return preg_replace(self::compileRegularExpressionPattern($pattern), '', $path);
    }

    public static function compileRegularExpressionPattern(
        string $pattern,
        bool $strictLeadingDot = true,
        bool $strictWildcardSlash = true
    ): string
    {
        $firstByte   = true;
        $escaping    = false;
        $inCurledBrackets   = 0;
        $patternSize = strlen($pattern);
        $regex       = '';

        for ($i = 0; $i < $patternSize; $i++) {
            $subject = $pattern[$i];

            if ($firstByte) {
                if ($strictLeadingDot && '.' !== $subject) {
                    $regex .= '(?=[^\.])';
                }

                $firstByte = false;
            }

            switch ($subject) {
                case '/':
                    $firstByte = true;

                case '.':
                case '(':
                case ')':
                case '|':
                case '+':
                case '^':
                case '$':
                    $regex .= '\\' . $subject;
                    break;

                case '[':
                case ']':
                    $regex .= $escaping
                        ? '\\' . $subject
                        : $subject;
                    break;

                case '*':
                    $regex .= $escaping ? '\\*' : ($strictWildcartSlash
                        ? '[^/]*'
                        : '.*');
                    break;

                case '?':
                    $regex .= $escaping ? '\\?' : ($strictWildcartSlash
                        ? '[^/]'
                        : '.');
                    break;

                case '{':
                    $regex .= !$escaping && ++$inCurledBrackets
                        ? '('
                        : '\\{';
                    break;

                case '}':
                    $regex .= !$escaping && $inCurledBrackets && $inCurledBrackets--
                        ? ')'
                        : '}';
                    break;

                case ',':
                    $regex .= !$escaping && $inCurledBrackets
                        ? '|'
                        : ',';
                    break;

                case '\\':
                    $regex .= $escaping
                        ? '\\\\'
                        : '';
                    $escaping = !$escaping;
                    continue 2;

                default:
                    $regex .= $subject;
            }

            $escaping = false;
        }

        return '#^(' . $regex . ')#';
    }

    public static function hasBit(int|BitmaskInterface|EnumerationInterface|Arrayable|iterable $subject, int|EnumerationInterface|BitmaskInterface $property): bool
    {
        if(ConcernUtility::isTraversable($subject)) {
            foreach($subject as $test) {
                if(self::hasBit($test, $property)) {
                    return true;
                }
            }
        } else {
            return BitmaskUtility::has(ConcernUtility::toInteger($property), ConcernUtility::toInteger($subject));
        }

        return false;
    }

    public static function copyToStream(): bool
    {
    }

    public static function convertStringToBitMode(string $string): ?string
    {
        if(strlen($string) === 3) {
            return ($string[0] ? 4 : 0)
                || ($string[1] ? 2 : 0)
                || ($string[2] ? 1 : 0);
        }

        if(strlen($string) === 9) {
            return '0' .
                self::convertStringToBitMode(substr($string, 0, 3)) .
                self::convertStringToBitMode(substr($string, 3, 3)) .
                self::convertStringToBitMode(substr($string, 6, 3));
        }

        if(strlen($string) === 10) {
            return self::convertStringToBitMode(substr($string, 1));
        }

        return null;
    }

    public static function getResource()
    {
        if(!self::$resource) {
            self::$resource = finfo_open();
        }

        return self::$resource;
    }

    public static function getPathnameComponents(string $pathname): array
    {
        if($pathname === '') {
            return [];
        }

        $pathname  = str_replace('\\', '/', $pathname);
        $pathname  = preg_replace('@^(?>[a-zA-Z]:)?[/\s]+|[/\s]+$@', '', $pathname);
        $components = [];

        foreach (explode('/', $pathname) as $part) {
            if ($part === '..') {
                array_pop($components);
            } else if($part !== '.' && $part !== '') {
                $components[] = $part;
            }
        }

        return $components;
    }

    public static function getDirectory(string $pathname): string
    {
        $index = strrpos($pathname, '/');

        if($index > 0) {
            return substr($pathname, 0, $index);
        }

        return '/';
    }

}