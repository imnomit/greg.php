<?php

namespace nomit\Resource\Hash;

use nomit\FileSystem\File\FileInterface;

class MetadataResolverHash extends AbstractResolverHash
{

    public function hash(): string
    {
        if($this->entity instanceof FileInterface) {
            $hash = new MetadataFileHash($this->entity, $this->hasher);
        } else {
            $hash = new MetadataDirectoryHash($this->entity, $this->hasher);
        }

        return $hash->hash();
    }

}