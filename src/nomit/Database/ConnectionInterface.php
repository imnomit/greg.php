<?php

namespace nomit\Database;

interface ConnectionInterface
{

    public function connect(): void;

    public function reconnect(): void;

    public function disconnect(): void;

    public function getDsn(): string;

    public function getPdo(): \PDO;

    public function getDriver(): Driver;

    public function getSupplementalDriver(): Driver;

    public function setRowNormalizer(?callable $normalizer): self;

    public function getInsertId(?string $sequence = null): string;

    public function quote(string $string): string;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;

    public function transaction(callable $callback): mixed;

    public function query(string $sql, ...$params): ResultSet;

    public function queryArgs(string $sql, array $params): ResultSet;

    public function preprocess(string $sql, ...$params): array;

    public function getLastQueryString(): ?string;

    public function fetch(string $sql, ...$params): ?Row;

    public function fetchField(string $sql, ...$params): mixed;

    public function fetchFields(string $sql, ...$params): ?array;

    public function fetchPairs(string $sql, ...$params): array;

    public function fetchAll(string $sql, ...$params): array;

    public static function literal(string $value, ...$params): SqlLiteral;

}