<?php
declare (strict_types=1);

namespace Captainbi\Hyperf\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

/**
 * HTTP异常
 */
class BusinessException extends ServerException implements Throwable
{
    public function __construct($code = 10001, $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
