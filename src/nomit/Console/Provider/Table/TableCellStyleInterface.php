<?php

namespace nomit\Console\Provider\Table;

interface TableCellStyleInterface
{

    public const DEFAULT_ALIGN = 'left';

    public const TAG_OPTIONS = [
        'fg',
        'bg',
        'options',
    ];

    public const ALIGN_MAP = [
        'left' => \STR_PAD_RIGHT,
        'center' => \STR_PAD_BOTH,
        'right' => \STR_PAD_LEFT,
    ];

    public function getOptions(): array;

    public function getTagOptions(): array;

    public function getPadByAlignment(): int;

    public function getCellFormat(): ?string;

}