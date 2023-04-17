<?php

namespace nomit\Routing\Utilities;

use nomit\Routing\Router;
use nomit\Utility\Object\StaticClassTrait;
use Psr\Http\Message\ResponseInterface;
use nomit\Routing\Exception\InvalidAttributeValueException;
use nomit\Routing\Exception\InvalidPathException;
use nomit\Routing\Exception\MissingAttributeValueException;

final class Utilities
{

    use StaticClassTrait;


    /**
     * Allowed characters for the first character of the subpattern name
     *
     * @var array
     */
    public const CHARACTER_TABLE_FOR_FIRST_CHARACTER_OF_SUBPATTERN_NAME = [
        'A' => 1, 'B' => 1, 'C' => 1, 'D' => 1, 'E' => 1, 'F' => 1, 'G' => 1, 'H' => 1, 'I' => 1, 'J' => 1,
        'K' => 1, 'L' => 1, 'M' => 1, 'N' => 1, 'O' => 1, 'P' => 1, 'Q' => 1, 'R' => 1, 'S' => 1, 'T' => 1,
        'U' => 1, 'V' => 1, 'W' => 1, 'X' => 1, 'Y' => 1, 'Z' => 1,
        'a' => 1, 'b' => 1, 'c' => 1, 'd' => 1, 'e' => 1, 'f' => 1, 'g' => 1, 'h' => 1, 'i' => 1, 'j' => 1,
        'k' => 1, 'l' => 1, 'm' => 1, 'n' => 1, 'o' => 1, 'p' => 1, 'q' => 1, 'r' => 1, 's' => 1, 't' => 1,
        'u' => 1, 'v' => 1, 'w' => 1, 'x' => 1, 'y' => 1, 'z' => 1,
        '_' => 1,
    ];

    /**
     * Allowed characters for the subpattern name
     *
     * @var array
     */
    public const CHARACTER_TABLE_FOR_SUBPATTERN_NAME = [
        '0' => 1, '1' => 1, '2' => 1, '3' => 1, '4' => 1, '5' => 1, '6' => 1, '7' => 1, '8' => 1, '9' => 1,
        'A' => 1, 'B' => 1, 'C' => 1, 'D' => 1, 'E' => 1, 'F' => 1, 'G' => 1, 'H' => 1, 'I' => 1, 'J' => 1,
        'K' => 1, 'L' => 1, 'M' => 1, 'N' => 1, 'O' => 1, 'P' => 1, 'Q' => 1, 'R' => 1, 'S' => 1, 'T' => 1,
        'U' => 1, 'V' => 1, 'W' => 1, 'X' => 1, 'Y' => 1, 'Z' => 1,
        'a' => 1, 'b' => 1, 'c' => 1, 'd' => 1, 'e' => 1, 'f' => 1, 'g' => 1, 'h' => 1, 'i' => 1, 'j' => 1,
        'k' => 1, 'l' => 1, 'm' => 1, 'n' => 1, 'o' => 1, 'p' => 1, 'q' => 1, 'r' => 1, 's' => 1, 't' => 1,
        'u' => 1, 'v' => 1, 'w' => 1, 'x' => 1, 'y' => 1, 'z' => 1,
        '_' => 1,
    ];


    public static function emit(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf(
                    '%s: %s',
                    $name,
                    $value
                ), false);
            }
        }

        header(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ), true);

        echo $response->getBody();
    }

    public static function buildPath(string $path, array $attributes = [], bool $strict = false): string
    {
        $result = $path;
        $matches = self::parsePath($path);

        foreach ($matches as $match) {
            // handle not required attributes...
            if (!isset($attributes[$match['name']])) {
                if (!$match['isOptional']) {
                    $errmsg = '[%s] build error: no value given for the attribute "%s".';

                    throw new MissingAttributeValueException(sprintf($errmsg, $path, $match['name']), [
                        'path' => $path,
                        'match' => $match,
                    ]);
                }

                $result = str_replace($match['withParentheses'], '', $result);

                continue;
            }

            $replacement = (string) $attributes[$match['name']];

            // validate the given attributes values...
            if ($strict && isset($match['pattern'])) {
                if (!preg_match('#^' . $match['pattern'] . '$#u', $replacement)) {
                    $errmsg = '[%s] build error: the given value for the attribute "%s" does not match its pattern.';

                    throw new InvalidAttributeValueException(sprintf($errmsg, $path, $match['name']), [
                        'path' => $path,
                        'value' => $replacement,
                        'match' => $match,
                    ]);
                }
            }

            $result = str_replace($match['raw'], $replacement, $result);
        }

        $result = str_replace(['(', ')'], '', $result);

        return $result;
    }
    
    public static function matchPath(string $path, string $subject, &$attributes = null): bool
    {
        $attributes = [];

        $regex = self::regexPath($path);

        if (!preg_match($regex, $subject, $matches)) {
            return false;
        }

        foreach ($matches as $key => $value) {
            if (!is_int($key) && '' !== $value) {
                $attributes[$key] = $value;
            }
        }

        return true;
    }

    public static function parsePath(string $path): array
    {
        // This will be useful for a long-running application,
        // for example if you use the RoadRunner server...
        static $cache = [];

        if (isset($cache[$path])) {
            return $cache[$path];
        }

        $attributes = [];
        $attributeIndex = -1;
        $attributePrototype = [
            'raw' => null,
            'withParentheses' => null,
            'name' => null,
            'pattern' => null,
            'isOptional' => false,
            'startPosition' => -1,
            'endPosition' => -1,
        ];

        $cursorPosition = -1;
        $cursorInParentheses = false;
        $cursorInAttribute = false;
        $cursorInAttributeName = false;
        $cursorInPattern = false;

        $parenthesesBusy = false;
        $parenthesesLeft = null;
        $parenthesesRight = null;

        while (true) {
            $cursorPosition++;

            if (!isset($path[$cursorPosition])) {
                break;
            }

            $char = $path[$cursorPosition];

            if ('(' === $char && !$cursorInAttribute) {
                if ($cursorInParentheses) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] parentheses inside parentheses are not allowed.', $path, $cursorPosition)
                    );
                }

                $cursorInParentheses = true;

                continue;
            }

            if ('{' === $char && !$cursorInPattern) {
                if ($cursorInAttribute) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] braces inside attributes are not allowed.', $path, $cursorPosition)
                    );
                }

                if ($parenthesesBusy) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] multiple attributes inside parentheses are not allowed.', $path, $cursorPosition)
                    );
                }

                if ($cursorInParentheses) {
                    $parenthesesBusy = true;
                }

                $cursorInAttribute = true;
                $cursorInAttributeName = true;

                $attributeIndex++;
                $attributes[$attributeIndex] = $attributePrototype;
                $attributes[$attributeIndex]['raw'] = $char;
                $attributes[$attributeIndex]['isOptional'] = $cursorInParentheses;
                $attributes[$attributeIndex]['startPosition'] = $cursorPosition;

                continue;
            }

            if ('<' === $char && $cursorInAttribute) {
                if ($cursorInPattern) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] the char "<" inside patterns is not allowed.', $path, $cursorPosition)
                    );
                }

                $cursorInPattern = true;
                $cursorInAttributeName = false;

                $attributes[$attributeIndex]['raw'] .= $char;

                continue;
            }

            if ('>' === $char && $cursorInAttribute) {
                if (!$cursorInPattern) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] at position %2$d an extra char ">" was found.', $path, $cursorPosition)
                    );
                }

                if (null === $attributes[$attributeIndex]['pattern']) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] an attribute pattern is empty.', $path, $cursorPosition)
                    );
                }

                if (isset(Router::$patterns[$attributes[$attributeIndex]['pattern']])) {
                    $attributes[$attributeIndex]['pattern'] = Router::$patterns[$attributes[$attributeIndex]['pattern']];
                }

                $cursorInPattern = false;

                $attributes[$attributeIndex]['raw'] .= $char;

                continue;
            }

            if ('}' === $char && !$cursorInPattern) {
                if (!$cursorInAttribute) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] at position %2$d an extra closing brace was found.', $path, $cursorPosition)
                    );
                }

                if (null === $attributes[$attributeIndex]['name']) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] an attribute name is empty.', $path, $cursorPosition)
                    );
                }

                $cursorInAttribute = false;
                $cursorInAttributeName = false;

                $attributes[$attributeIndex]['raw'] .= $char;
                $attributes[$attributeIndex]['endPosition'] = $cursorPosition;

                continue;
            }

            if (')' === $char && !$cursorInAttribute) {
                if (!$cursorInParentheses) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] at position %2$d an extra closing parenthesis was found.', $path, $cursorPosition)
                    );
                }

                if ($parenthesesBusy) {
                    $attributes[$attributeIndex]['withParentheses'] = '(' . $parenthesesLeft;
                    $attributes[$attributeIndex]['withParentheses'] .= $attributes[$attributeIndex]['raw'];
                    $attributes[$attributeIndex]['withParentheses'] .= $parenthesesRight . ')';
                }

                $cursorInParentheses = false;
                $parenthesesBusy = false;
                $parenthesesLeft = null;
                $parenthesesRight = null;

                continue;
            }

            if ($cursorInParentheses && !$cursorInAttribute && !$parenthesesBusy) {
                $parenthesesLeft .= $char;
            }

            if ($cursorInParentheses && !$cursorInAttribute && $parenthesesBusy) {
                $parenthesesRight .= $char;
            }

            if ($cursorInAttribute) {
                $attributes[$attributeIndex]['raw'] .= $char;
            }

            if ($cursorInAttributeName) {
                if (null === $attributes[$attributeIndex]['name']) {
                    if (!isset(self::CHARACTER_TABLE_FOR_FIRST_CHARACTER_OF_SUBPATTERN_NAME[$char])) {
                        throw new InvalidPathException(
                            sprintf('[%s:%d] an attribute name must begin with "A-Za-z_".', $path, $cursorPosition)
                        );
                    }
                }

                if (null !== $attributes[$attributeIndex]['name']) {
                    if (!isset(self::CHARACTER_TABLE_FOR_SUBPATTERN_NAME[$char])) {
                        throw new InvalidPathException(
                            sprintf('[%s:%d] an attribute name must contain only "0-9A-Za-z_".', $path, $cursorPosition)
                        );
                    }
                }

                $attributes[$attributeIndex]['name'] .= $char;
            }

            if ($cursorInPattern) {
                if ('#' === $char) {
                    throw new InvalidPathException(
                        sprintf('[%s:%d] unallowed character "#" in an attribute pattern.', $path, $cursorPosition)
                    );
                }

                $attributes[$attributeIndex]['pattern'] .= $char;
            }
        }

        if ($cursorInParentheses) {
            throw new InvalidPathException(
                sprintf('[%s] the route path contains non-closed parentheses.', $path)
            );
        }

        if ($cursorInAttribute) {
            throw new InvalidPathException(
                sprintf('[%s] the route path contains non-closed attribute.', $path)
            );
        }

        $cache[$path] = $attributes;

        return $attributes;
    }

    public static function plainPath(string $path): string
    {
        $attrs = self::parsePath($path);

        foreach ($attrs as $attr) {
            $path = str_replace($attr['raw'], '{' . $attr['name'] . '}', $path);
        }

        return str_replace(['(', ')'], '', $path);
    }

    public static function regexPath(string $path): string
    {
        // This will be useful for a long-running application,
        // for example if you use the RoadRunner server...
        static $cache = [];

        if (isset($cache[$path])) {
            return $cache[$path];
        }

        $matches = self::parsePath($path);

        foreach ($matches as $match) {
            $path = str_replace($match['raw'], '{' . $match['name'] . '}', $path);
        }

        $path = addcslashes($path, '#$*+-.?[\]^|');
        $path = str_replace(['(', ')'], ['(?:', ')?'], $path);

        foreach ($matches as $match) {
            $pattern = $match['pattern'] ?? '[^/]+';
            $subpattern = '(?<' . $match['name'] . '>' . $pattern . ')';

            $path = str_replace('{' . $match['name'] . '}', $subpattern, $path);
        }

        $cache[$path] = '#^' . $path . '$#uD';

        return $cache[$path];
    }

}