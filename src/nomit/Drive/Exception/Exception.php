<?php

namespace nomit\Drive\Exception;

use nomit\Utility\Bitmask\Bitmask;
use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Integerable;
use nomit\Utility\EnumerationUtility;

class Exception extends \nomit\Exception\Exception implements ExceptionInterface,
    Integerable
{

    protected BitmaskInterface $mask;

    protected static function getSupportedCodes(): array
    {
        return array_map(function(\UnitEnum $enumeration) {
            return sprintf('%s [%s]', $enumeration->value, $enumeration->name);
        }, ExceptionCodeEnumeration::cases());
    }

    public function __construct(
        string                       $message = "",
        int|ExceptionCodeEnumeration $code = 0,
        ?\Throwable                   $previous = null
    )
    {
        $code = ConcernUtility::toInteger($code);

        if(!EnumerationUtility::supports($code, ExceptionCodeEnumeration::class)) {
            throw new parent(sprintf('The supplied exception code, "%s", is not supported by the "%s" exception class. The supported exception codes, all cases of the enumeration "%s", are as follows: "%s".', $code, self::class, ExceptionCodeEnumeration::class, implode(', ', self::getSupportedCodes())));
        }

        $this->mask = new Bitmask($code);

        parent::__construct($message, $this->mask->get(), $previous);
    }

    public function getMask(): BitmaskInterface
    {
        return $this->mask;
    }

    public function isFileSystemException(): bool
    {
        return $this->mask->has(ExceptionCodeEnumeration::FILESYSTEM_EXCEPTION);
    }

    public function isOperationException(): bool
    {
        return $this->mask->has(ExceptionCodeEnumeration::OPERATION_EXCEPTION);
    }

    public function isNotFoundException(): bool
    {
        return $this->mask->has(ExceptionCodeEnumeration::NOT_FOUND_EXCEPTION);
    }

    public function isNotFileException(): bool
    {
        return $this->mask->has(ExceptionCodeEnumeration::NOT_FILE_EXCEPTION);
    }

    public function isNotDirectoryException(): bool
    {
        return $this->mask->has(ExceptionCodeEnumeration::NOT_DIRECTORY_EXCEPTION);
    }

    public function toInteger(): int
    {
        return $this->mask->get();
    }

    public function toString(): string
    {
        return $this->getMessage();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}