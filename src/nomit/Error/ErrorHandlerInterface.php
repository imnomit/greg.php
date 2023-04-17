<?php

namespace nomit\Error;

use nomit\Error\Handler\HandlerInterface;
use nomit\Error\View\ViewInterface;
use nomit\Web\Response\ResponseInterface;
use Psr\Log\LoggerInterface;

interface ErrorHandlerInterface
{

    public static function register(self $instance = null): self;

    public static function call(callable $function, mixed ...$arguments): mixed;

    public static function handleFatalError(array $error = null): void;

    public function setDefaultLogger(LoggerInterface $logger, array|int|null $levels = \E_ALL, bool $replace = false): self;

    public function setLoggers(array $loggers): array;

    public function throwAt(int $levels, bool $replace = false): int;

    public function scopeAt(int $levels, bool $replace = false): int;

    public function traceAt(int $levels, bool $replace = false): int;

    public function screamAt(int $levels, bool $replace = false): int;

    public function setFallbackView(ViewInterface $view): self;

    public function getFallbackView(): ?ViewInterface;

    public function handleError(int $type, string $message, string $file, int $line): bool;

    public function handleException(\Throwable $exception, bool $send = true): ?ResponseInterface;

    public function setHandlers(array $handlers): self;

    public function pushHandler(HandlerInterface $handler): self;

    public function popHandler(): ?HandlerInterface;

    public function getHandlers(): array;

    public function clearHandlers(): self;

    public function setViews(array $views): self;

    public function pushView(ViewInterface $view): self;

    public function popView(): ?ViewInterface;

    public function getViews(): array;

    public function clearViews(): self;

}