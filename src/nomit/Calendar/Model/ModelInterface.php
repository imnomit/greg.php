<?php

namespace nomit\Calendar\Model;

interface ModelInterface extends \nomit\Utility\Concern\Arrayable, \nomit\Utility\Concern\Serializable
{

    public static function fromArray(array $data): self;

    public function getId(): int;

}