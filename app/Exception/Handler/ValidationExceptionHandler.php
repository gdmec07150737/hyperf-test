<?php

declare(strict_types=1);

namespace App\Exception\Handler;


use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        /** @var ValidationException $throwable */
        $body = $throwable->validator->errors()->first();
        if (! $response->hasHeader('content-type')) {
            $response = $response->withAddedHeader('content-type', 'text/plain; charset=utf-8');
        }
        return $response->withStatus($throwable->status)->withBody(new SwooleStream(json_encode(['code' => 500, 'msg' => $body])));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}