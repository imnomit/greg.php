<?php

namespace nomit\Resource\Hash;

class LastModifiedFileHash extends AbstractFileHash
{

    public function hash(): string
    {
        return $this->hasher->make($this->file->getLastModifiedTime());
    }

}