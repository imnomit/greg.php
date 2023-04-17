<?php

namespace nomit\Resource\Hash;

use nomit\Dumper\Dumper;
use nomit\FileSystem\File\File;

final class LastModifiedDirectoryContentsHash extends AbstractDirectoryHash
{

    public function hash(): string
    {
        $directoryContents = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->directory->getPathname(), \RecursiveDirectoryIterator::SKIP_DOTS));
        $lastModifiedTimestamps = [];

        foreach($directoryContents as $directoryContent) {
            if($directoryContent->isFile()) {
                $lastModifiedTimestamps[] = $directoryContent->getMTime();
            }
        }

        $lastModifiedTimestamps = array_unique($lastModifiedTimestamps);
                                  rsort($lastModifiedTimestamps);

        $lastModified = max($lastModifiedTimestamps);

        return $this->hasher->make($lastModified);
    }

}