<?php

namespace nomit\Drive\Utility;

use nomit\Utility\Enumeration\FlaggableEnumerationInterface;
use nomit\Utility\Enumeration\FlaggableEnumerationTrait;
use nomit\Utility\Enumeration\SelfAwareEnumerationInterface;
use nomit\Utility\Enumeration\SelfAwareEnumerationTrait;

enum OperationEnumeration: int implements SelfAwareEnumerationInterface,
    FlaggableEnumerationInterface
{

    use SelfAwareEnumerationTrait,
        FlaggableEnumerationTrait;

    /**
     * @var int Flag for file operations, which involve a file target
     *      destination, indicating that missing parent directories of the
     *      operations file target destination should be created before the
     *      execution of the operation is done
     */
    case OPERATION_PARENTS = 0x01;

    /**
     * @var int Flag for file operations to apply the execution recrusivly, if
     *      the file being operated on is a directory.
     */
    case OPERATION_RECURSIVE = 0x02;

    /**
     * @var int Flag for file operations, which involve a file target
     *      destination, to reject the execution of the operation, if the
     *      operations file target destination already exists. This flag
     *      overrules the OPERATION_REPLACE flag.
     */
    case OPERATION_REJECT = 0x10;

    /**
     * @var int Flag for file operations, which involve a file target
     *      destination, to merge the operations file source with the operations
     *      file target destination.
     */
    case OPERATION_MERGE = 0x20;

    /**
     * @var int Flag for file operations, which involve a file target
     *      destination, to replace the target destination.
     */
    case OPERATION_REPLACE = 0x40;

    public function isParentsOperation(): bool
    {
        return $this->has(self::OPERATION_PARENTS);
    }

    public function isRecursiveOperation(): bool
    {
        return $this->has(self::OPERATION_RECURSIVE);
    }

    public function isRejectOperation(): bool
    {
        return $this->has(self::OPERATION_REJECT);
    }

    public function isMergeOperation(): bool
    {
        return $this->has(self::OPERATION_MERGE);
    }

    public function isReplaceOperation(): bool
    {
        return $this->has(self::OPERATION_REPLACE);
    }

}