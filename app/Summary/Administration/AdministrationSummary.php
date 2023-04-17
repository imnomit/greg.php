<?php

namespace Application\Summary\Administration;

final class AdministrationSummary implements AdministrationSummaryInterface
{

    private array $extensions = [];

    public function __construct(
        array $extensions = []
    )
    {
    }

    public function addExtension(SummaryExtensionInterface $extension): self
    {
        $this->extensions[] = $extension;

        return $this;
    }

    public function getExtensions(): array{
        return $this->extensions;
    }

    public function summarize(): array
    {
        foreach($this->extensions as $extension) {
            $extension->summarize();
        }

        return $this->toArray();
    }

    public function toArray(): array
    {
        $data = [];

        foreach($this->extensions as $extension) {
            $data[$extension->getName()] = $extension->toArray();
        }

        return $data;
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}