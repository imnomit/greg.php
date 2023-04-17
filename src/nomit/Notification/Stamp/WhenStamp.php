<?php

namespace nomit\Notification\Stamp;

final class WhenStamp extends AbstractStamp
{

    public function __construct(
        private bool $condition
    )
    {
    }

    public function getName(): string
    {
        return 'when';
    }

    /**
     * @return bool
     */
    public function getCondition(): bool
    {
        return $this->condition;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'condition' => $this->getCondition()
        ];
    }

}