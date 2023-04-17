<?php

namespace nomit\Security\Registration\Exception;

use nomit\Security\Registration\RegistrationInterface;
use Psr\Http\Message\UploadedFileInterface;
use Throwable;

class MakeResourceRegistrationException extends RegistrationException
{

    protected UploadedFileInterface $uploaded_file;

    public function __construct(RegistrationInterface $registration, UploadedFileInterface $uploadedFile)
    {
        $this->uploaded_file = $uploadedFile;

        parent::__construct(
            $registration,
            sprintf('An error occurred while attempting to incorporate into the profile of the current user, user ID "%s" and username "%s", the uploaded file with a client file name and media type of "%s" and "%s", respectively.', $registration->getUserId(), $registration->getUsername(), $uploadedFile->getClientFilename(), $uploadedFile->getClientMediaType())
        );
    }

    public function getUploadedFile(): UploadedFileInterface
    {
        return $this->uploaded_file;
    }

}