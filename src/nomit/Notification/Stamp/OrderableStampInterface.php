<?php

namespace nomit\Notification\Stamp;

interface OrderableStampInterface extends StampInterface
{

    public function compare(mixed $orderable): int;

}