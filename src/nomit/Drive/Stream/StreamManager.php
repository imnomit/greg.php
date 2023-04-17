<?php

namespace nomit\Drive\Stream;

use nomit\Drive\Exception\Stream\AlreadyRegisteredStreamWrapperException;
use nomit\Drive\Exception\Stream\UnregisteredStreamWrapperException;
use nomit\Drive\FileSystemInterface;
use nomit\Drive\Stream\StreamInterface;

final class StreamManager implements StreamManagerInterface
{

    protected static array $fileSystems = [];

    protected static int $autoregisteredFileSystemIndex = 0;

    protected static array $streams = [];

    protected static int $streamIndex = 0;

    public static function autoregister(FileSystemInterface $fileSystem): array
    {
        $scheme = 'nomitfs';
        $host = 'automount:' . self::$autoregisteredFileSystemIndex++;

        self::register($fileSystem, $host, $scheme);

        return [$host, $scheme];
    }

    public static function register(FileSystemInterface $fileSystem, string $host, string $scheme = null): void
    {
        if(!$scheme) {
            $scheme = 'nomitfs';
        }

        $registeredWrappers = stream_get_wrappers();

        if((isset(self::$fileSystems[$scheme]) && isset(self::$fileSystems[$scheme][$host]))
            || (!isset(self::$fileSystems[$scheme]) && in_array($scheme, $registeredWrappers))
        ) {
            throw new AlreadyRegisteredStreamWrapperException($scheme, $host);
        }

        if(!isset(self::$fileSystems[$scheme])) {
            self::$fileSystems[$scheme] = [];

            stream_wrapper_register($scheme, StreamWrapper::class, STREAM_IS_URL);
        }

        self::$fileSystems[$scheme][$host] = $fileSystem;
    }

    public static function unregister(string $host, string $scheme, bool $silent = false): void
    {
        if(!$scheme) {
            $scheme = 'nomitfs';
        }

        if(!isset(self::$fileSystems[$scheme], self::$fileSystems[$scheme][$host])
        ) {
            if($silent) {
                return;
            }

            throw new UnregisteredStreamWrapperException($scheme, $host);
        }

        unset(self::$fileSystems[$scheme][$host]);
    }

    public static function search(string $host, string $scheme = null): FileSystemInterface
    {
        if(!$scheme) {
            $scheme = 'nomitfs';
        }

        if(!isset(self::$fileSystems[$scheme], self::$fileSystems[$scheme][$host])) {
            throw new AlreadyRegisteredStreamWrapperException($scheme, $host);
        }

        return self::$fileSystems[$scheme][$host];
    }

    public static function registerStream(StreamInterface $stream): int
    {
        if(empty(self::$streams)) {
            stream_wrapper_register('nomitfs-streams', StreamWrapper::class, STREAM_IS_URL);
        }

        self::$streams[self::$streamIndex++] = $stream;

        return self::$streamIndex;
    }

    public static function unregisterStream(StreamInterface $stream): void
    {
        foreach(self::$streams as $index => $registeredStream) {
            if($registeredStream === $stream) {
                unset(self::$streams[$index]);
            }
        }
    }

    public static function searchStream(int $index): StreamInterface
    {
        return self::$streams[$index];
    }

    public static function free(): void
    {
        foreach(self::$fileSystems as $scheme => $map) {
            if(empty($map)) {
                stream_wrapper_unregister($scheme);

                unset(self::$fileSystems[$scheme]);
            }
        }
    }

    private function __construct()
    {
    }

}