<?php

namespace Application\Resource;

use nomit\Web\Response\ResponseInterface;

interface ResourceDumperInterface
{

    public function dumpManifest(string $filename): ResponseInterface;

    public function dumpResource(string $filename, string $version): ResponseInterface;

}