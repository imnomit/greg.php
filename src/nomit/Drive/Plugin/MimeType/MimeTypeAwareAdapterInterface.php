<?php

namespace nomit\Drive\Plugin\MimeType;

use nomit\Drive\Adapter\AdapterInterface;
use nomit\Drive\Pathname\PathnameInterface;

interface MimeTypeAwareAdapterInterface extends AdapterInterface
{

    public function getMimeName(PathnameInterface $pathname): string;

    public function getMimeType(PathnameInterface $pathname): string;

    public function getMimeEncoding(PathnameInterface $pathname): string;

}