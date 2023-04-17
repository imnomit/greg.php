<?php

namespace nomit\Resource\Resource\UploadedFile\Event;

use nomit\EventDispatcher\Event;
use nomit\Resource\UploadedFileResource;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFileResourceEvent
 * @package nomit\Resource\resource\UploadedFile\Event
 */
class UploadedFileResourceEvent extends Event
{

    public const NAME = 'resource.resource.uploaded_file';

    /**
     * @var UploadedFileResource
     */
    protected $resource;

    /**
     * UploadedFileResourceEvent constructor.
     * @param UploadedFileResource $resource
     */
    public function __construct(UploadedFileResource $resource)
    {
        $this->setResource($resource);
    }

    /**
     * @param UploadedFileResource $resource
     * @return $this
     */
    public function setResource(UploadedFileResource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return UploadedFileResource
     */
    public function getResource(): UploadedFileResource
    {
        return $this->resource;
    }

}