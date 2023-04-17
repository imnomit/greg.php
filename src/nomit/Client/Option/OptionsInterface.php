<?php

namespace nomit\Client\Option;

use nomit\Utility\Concern\Arrayable;

interface OptionsInterface extends Arrayable
{

    public function setAuthenticationBasic(string $user, string $password = ''): self;

    public function setAuthenticationBearer(string $token): self;

    public function setQuery(array $query): self;

    public function setHeaders(iterable $headers): self;

    public function setBody(mixed $body): self;

    public function setJson(mixed $json): self;

    public function setUserData(mixed $data): self;

    public function setMaximumRedirects(int $maximum): self;

    public function setHttpVersion(string $version): self;

    public function setBaseUri(string $uri): self;

    public function buffer(bool $buffer): self;

    public function setOnProgress(callable $callback): self;

    public function resolve(array $hostIps): self;

    public function setProxy(string $proxy): self;

    public function setNoProxy(string $noProxy): self;

    public function setTimeout(float $timeout): self;

    public function bindTo(string $bindTo): self;

    public function verifyPeer(bool $verify): self;

    public function verifyHost(bool $verify): self;

    public function setCaFile(string $cafile): self;

    public function setCaPath(string $capath): self;

    public function setLocalCertificate(string $certificate): self;

    public function setLocalPk(string $pk): self;

    public function setPassphrase(string $passphrase): self;

    public function setCiphers(string $ciphers): self;

    public function setPeerFingerprint(string|array $fingerprint): self;

    public function capturePeerCertChain(bool $capture): self;

    public function setExtra(string $name, mixed $value): self;

}