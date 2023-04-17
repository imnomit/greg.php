<?php

namespace nomit\Resource\Hash;

final class MetadataFileHash extends AbstractFileHash
{

    public function hash(): string
    {
        $metadata = $this->file->getMetadata();
        $metadata = $this->makeString($metadata);

        return $this->hasher->make($metadata);
    }

    private function makeString(array $metadata): string
    {
        $subject = '';

        $subject .= 'size::' . $metadata['size'];
        $subject .= 'mtime::' . $metadata['mtime'];
        $subject .= 'ctime::' . $metadata['ctime'];

        return $subject;
    }

}