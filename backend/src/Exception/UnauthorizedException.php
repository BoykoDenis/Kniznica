<?php
declare(strict_types=1);

namespace Enm\JsonApi\Exception;
use Enm\JsonApi\Exception\JsonApiException;

class UnauthorizedException extends JsonApiException
{
    /**
     * @return int
     */
    public function getHttpStatus(): int
    {
        return 401;
    }
}
