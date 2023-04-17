<?php

namespace nomit\Console\Provider\Table;

use nomit\Utility\Concern\Stringable;

interface TableCellInterface extends Stringable
{

    public function getColumnSpan(): int;

    public function getRowSpan(): int;

    public function getStyle(): ?TableCellStyleInterface;

}