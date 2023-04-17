<?php

namespace nomit\Resource\Version;

use nomit\FileSystem\File\File;
use nomit\Resource\Hash\LastModifiedFileHash;
use nomit\Resource\Hash\LastModifiedResolverHash;

class LastModifiedFileVersion extends AbstractFileVersion
{

    public function getVersion(): string|int
    {
        $hash = new LastModifiedResolverHash($this->file);

        return $hash->hash();
    }

}