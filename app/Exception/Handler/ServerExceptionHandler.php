<?php

declare(strict_types=1);

namespace App\Exception\Handler;


use App\Exception\ServerException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ServerExceptionHandler extends ExceptionHandler
{

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
//        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
//        $this->logger->error($throwable->getTraceAsString());
        // 格式化输出
        $data = json_encode([
            'code' => $throwable->getCode(),
            'msg' => $throwable->getMessage(),
        ], JSON_UNESCAPED_UNICODE);

        // 阻止异常冒泡
        $this->stopPropagation();
        return $response->withStatus($throwable->getCode())->withBody(new SwooleStream($data));
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ServerException;
    }
}