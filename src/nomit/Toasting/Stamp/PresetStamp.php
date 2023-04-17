<?php

namespace nomit\Toasting\Stamp;

final class PresetStamp extends AbstractStamp
{

    public function __construct(
        private string $preset,
        private array $parameters = []
    )
    {
    }

    /**
     * @return string
     */
    public function getPreset(): string
    {
        return $this->preset;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function toArray(): array
    {
        return [
            'preset' => $this->getPreset(),
            'parameters' => $this->getParameters()
        ];
    }

}