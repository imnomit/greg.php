<?php

namespace nomit\Client\Exception;

final class RedirectionException extends RuntimeException implements RedirectionExceptionInterface
{

    use WebExceptionTrait;

}