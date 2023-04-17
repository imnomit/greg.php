<?php

namespace nomit\Utility\Callback;

use nomit\Exception\BadMethodCallException;
use nomit\Utility\Bitmask\BitmaskInterface;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\ConcernUtility;
use nomit\Utility\Concern\Stringable;
use nomit\Utility\Enumeration\EnumerationInterface;

final class CallbackUtility
{

    public static function callSafely(
        callable $callback,
        array|Arrayable $arguments = [],
        string|Stringable $exceptionClassName = null,
        int|EnumerationInterface|BitmaskInterface $exceptionCode = 0,
        string|Stringable $exceptionMessage = null
    ): mixed
    {
        $exception = null;

        try {
            $result = $callback(ConcernUtility::toArray($arguments));
        } catch(\Throwable $exception) {
            $result = false;
        }

        if(($exception !== null || $result === false)
            && $exceptionClassName !== null
        ) {
            $exceptionClassName = ConcernUtility::toString($exceptionClassName);

            if(!class_exists($exceptionClassName)) {
                throw new BadMethodCallException(sprintf('The exception class referenced by the supplied exception classname, "%s", does not exist.', $exceptionClassName));
            }

            if($exceptionMessage === null) {
                $exceptionMessage = sprintf('An error occurred while attempting to safely execute the supplied callback function via the "%s" method of the "%s" utility class.', __FUNCTION__, __CLASS__);
            } else {
                $exceptionMessage = ConcernUtility::toString($exceptionMessage);
            }

            throw new $exceptionClassName(
                vsprintf(
                    $exceptionMessage,
                    array_slice(
                        func_get_args(),
                        4
                    )
                ),
                ConcernUtility::toInteger($exceptionCode),
                $exception
            );
        }

        return $result;
    }

}