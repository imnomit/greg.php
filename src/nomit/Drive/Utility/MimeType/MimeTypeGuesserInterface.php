<?php

namespace nomit\Drive\Utility\MimeType;

/**
 * Interface Guesser
 *
 * @package nomit\HarDriver\Mimer\Interfaces
 */
interface MimeTypeGuesserInterface
{

    /**
     * @param string $subject
     * @return string String if guess is successful, NULL if not.
     */
    public static function guess(string $subject): ?string;

}