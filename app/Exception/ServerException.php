<?php


namespace App\Exception;


use Hyperf\HttpMessage\Server\Response;
use RuntimeException;
use Throwable;

class ServerException extends RuntimeException
{
    public function __construct($message = "", $code = 500, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = Response::getReasonPhraseByCode($code);
        }
        parent::__construct($message, $code, $previous);
    }
}