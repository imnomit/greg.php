<?php

namespace nomit\Client\Exception;

final class ServerException extends RuntimeException implements ServerExceptionInterface
{

    use WebExceptionTrait;

}