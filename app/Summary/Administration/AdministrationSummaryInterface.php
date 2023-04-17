<?php

namespace Application\Summary\Administration;

use nomit\Utility\Concern\Arrayable;

interface AdministrationSummaryInterface extends Arrayable
{

    public function addExtension(SummaryExtensionInterface $extension): self;
    
    public function getExtensions(): array;

    public function summarize(): array;

}