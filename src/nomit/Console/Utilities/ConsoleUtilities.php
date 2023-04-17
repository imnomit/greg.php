<?php

namespace nomit\Console\Utilities;

class ConsoleUtilities
{

    public static function toArray(string|array $variable): array
    {
        if(!is_array($variable)) {
            return [$variable];
        }

        return $variable;
    }

    public static function flatten(array $array): array
    {
        $flattened = [];

        array_walk_recursive($array, function ($array) use (&$flattened) {
            $flattened[] = $array;
        });

        return $flattened;
    }

    public static function snakeCase(string $string): string
    {
        return strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $string));
    }

}