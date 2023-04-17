<?php

namespace nomit\Cryptography;

use nomit\Cryptography\Hasher\HasherManager;
use nomit\Dependency\ContainerInterface;
use nomit\Utility\Facade\Container;

class Hasher
{

    /**
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        return Container::getFacadeContainer();
    }

    /**
     * @return string
     */
    protected static function getDefaultHasher(): string
    {
        return self::getContainer()->getParameter('security.cryptography.hasher.default');
    }

    /**
     * @param string $hashedValue
     * @return array
     */
    public static function info(string $hashedValue): array
    {
        return HasherManager::factory()->info($hashedValue);
    }

    /**
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function make(string $value, array $options = []): string
    {
        return HasherManager::factory()->make($value, $options);
    }

    /**
     * @param string $value
     * @param string $hashedValue
     * @param array $options
     * @return bool
     */
    public static function check(string $value, string $hashedValue, array $options = []): bool
    {
        return HasherManager::factory()->check($value, $hashedValue, $options);
    }

    /**
     * @param string $hashedValue
     * @param array $options
     * @return bool
     */
    public static function needsRehash(string $hashedValue, array $options = []): bool
    {
        return HasherManager::factory()->needsRehash($hashedValue, $options);
    }

    /**
     * @return string
     */
    public static function getDefaultDriver(): string
    {
        return HasherManager::factory()->getDefaultDriver();
    }

}