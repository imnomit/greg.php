<?php

namespace nomit\Web\File;

use Psr\Http\Message\UploadedFileInterface;

/**
 * Interface UploadedFileAwareInterface
 * @package nomit\Web\File
 */
interface UploadedFileAwareInterface
{

    /**
     * @param UploadedFileInterface $uploadedFile
     * @return $this
     */
    public function setUploadedFile(UploadedFileInterface $uploadedFile): self;

    /**
     * @return UploadedFileInterface
     */
    public function getUploadedFile(): UploadedFileInterface;

}