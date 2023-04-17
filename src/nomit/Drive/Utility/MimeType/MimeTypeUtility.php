<?php

namespace nomit\Drive\Utility\MimeType;

use nomit\Drive\Exception\ExceptionCodeEnumeration;
use nomit\Drive\Exception\OperationException;
use nomit\Drive\Exception\Resource\UnreadableResourceException;
use nomit\Drive\Resource\File\FileInterface;
use nomit\Drive\Utility\FileSystemUtility;
use nomit\Utility\Callback\CallbackUtility;

final class MimeTypeUtility
{

    public static function getMimeNameFromFile(string|\SplFileInfo|\SplFileObject|FileInterface $file): string
    {
        $file = self::normalizeFile($file);
        $contents = file_get_contents($pathname = $file->getPathname());

        return CallbackUtility::callSafely(
            function() use($contents) {
                return finfo_buffer(
                    FileSystemUtility::getResource(),
                    $contents,
                    FILEINFO_NONE
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'An error occurred while attempting to determine the MIME name of the file referenced by the supplied pathname, "%s".',
            $pathname
        );
    }

    public static function getMimeEncodingByFile(string|\SplFileInfo|\SplFileObject|FileInterface $file): string
    {
        $file = self::normalizeFile($file);
        $contents = file_get_contents($pathname = $file->getPathname());

        return CallbackUtility::callSafely(
            function() use($contents) {
                return finfo_buffer(
                    FileSystemUtility::getResource(),
                    $contents,
                    FILEINFO_MIME_ENCODING
                );
            },
            [],
            OperationException::class,
            ExceptionCodeEnumeration::OPERATION_EXCEPTION,
            'An error occurred while attempting to determine the MIME name of the file referenced by the supplied pathname, "%s".',
            $pathname
        );
    }

    private static function normalizeFile(string|\SplFileInfo|\SplFileObject|FileInterface $file): \SplFileInfo
    {
        if($file instanceof FileInterface) {
            $file = $file->getPathname();
        }

        if(is_string($file)) {
            $file = new \SplFileInfo($file);
        }

        if(!$file->isReadable()) {
            throw new UnreadableResourceException($file->getPathname());
        }

        return $file;
    }

    public static function getMimeTypeFromFilename(string $filename): ?string
    {
        return ExtensionToMimeTypeUtility::guessFromFilename($filename);
    }

    public static function getMimeTypeFromExtension(string $extension): ?string
    {
        return ExtensionToMimeTypeUtility::guess($extension);
    }

    public static function getMimeTypeFromFontAwesome(string $file, bool $fixedWidth): string
    {
        return ExtensionToMimeTypeUtility::guessFromFontAwesome($file, $fixedWidth);
    }

    public static function getMimeTypeFromFile(string|\SplFileInfo|\SplFileObject|FileInterface $file): string
    {
        if($file instanceof FileInterface) {
            $file = $file->getPathname();
        }

        return ExtensionToMimeTypeUtility::guessFromFile($file);
    }

    public function getExtensionFromMimeType(string $mimeType): ?string
    {
        return MimeTypeToExtensionUtility::guess($mimeType);
    }

    public function isImageExtension(string $extension): bool
    {
        return ImageIdentifierUtility::guessByExtension($extension);
    }

    public function isImageMimeType(string $mimeType): bool
    {
        return ImageIdentifierUtility::guessByMimeType($mimeType);
    }

    public function isTextExtension(string $extension): bool
    {
        return TextIdentifierUtility::guessByExtension($extension);
    }

    public function isTextMimeType(string $mimeType): bool
    {
        return TextIdentifierUtility::guessByMimeType($mimeType);
    }

    public function isCodeExtension(string $extension): bool
    {
        return TextIdentifierUtility::guessIsCodeByExtension($extension);
    }

    public function isCodeMimeType(string $mimeType): bool
    {
        return TextIdentifierUtility::guessIsCodeByMimeType($mimeType);
    }

}