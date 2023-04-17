<?php

namespace nomit\Resource\Hash;

class LastModifiedDirectoryHash extends AbstractDirectoryHash
{

    public function hash(): string
    {
        return $this->hasher->make($this->directory->getLastModifiedTime());
    }

}