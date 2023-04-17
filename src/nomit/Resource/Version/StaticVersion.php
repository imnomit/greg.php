<?php

namespace nomit\Resource\Version;

final class StaticVersion extends AbstractVersion
{

    public function __construct(
        protected string|int $version,
        protected string $format
    )
    {
    }

}