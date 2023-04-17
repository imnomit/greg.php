<?php

namespace nomit\Mail\Mime;

interface MimePartInterface
{

    public const
        ENCODING_BASE64 = 'base64',
        ENCODING_7BIT = '7bit',
        ENCODING_8BIT = '8bit',
        ENCODING_QUOTED_PRINTABLE = 'quoted-printable';

    public const EOL = "\r\n";

    public const LineLength = 76;

    public function setHeader(string $name, string|array|null $value, bool $append = false): self;
    
    public function getHeader(string $header): mixed;
    
    public function clearHeader(string $header): self;
    
    public function getEncodedHeader(string $name): ?string;
    
    public function getHeaders(): array;
    
    public function setContentType(string $contentType, ?string $charset = null): self;
    
    public function setEncoding(string $encoding): self;
    
    public function getEncoding(): string;
    
    public function addPart(?self $part = null): self;
    
    public function setBody(string $body): self;
    
    public function getBody(): string;

    public function getEncodedMessage(): string;

}