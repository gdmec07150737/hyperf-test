<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ServerException;
use App\Model\User;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VerifyLogin implements MiddlewareInterface
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
        $action = $request->getAttribute(Dispatched::class)->handler->callback;
        //用户控制器除了登录和注册方法都需要验证登录
        if ($action[0] === 'App\Controller\UserController' && ($action[1] !== 'Registered' && $action[1] !== 'Login')) {
            $authorization = str_replace("Bearer ", "", $request->getHeader('Authorization'));
            if (!$authorization[0]) {
                throw new ServerException("没有token，请重新登录！", 500);
            }
            /** @var User $user */
            $user = User::query()->where('token', $authorization[0])->first();
            if (!$user) {
                throw new ServerException("该用户账号不存在，请重新登录！", 500);
            }
        }

        return $handler->handle($request);
    }
}