<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Middleware\Auth;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FooMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo 1 . "\n";
        echo "全局\n";
//        $request = $request->withAttribute('testName', 'value8');
        $request = Context::override(ServerRequestInterface::class, function () use ($request) {
            return $request->withAttribute('testName', 'value8');
        });

        $response = $handler->handle($request);

        $response = Context::override(ResponseInterface::class, function () use ($response) {
            return $response->withBody(new SwooleStream($response->getBody()->getContents() . 'foo'));
        });
        echo 6 . "\n";
        return $response;
    }
}
