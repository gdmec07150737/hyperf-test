<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ServerException;
use App\Model\User;
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

        //用来需要验证登录的功能
        $authorization = str_replace("Bearer ", "", $request->getHeader('Authorization'));
        if (!isset($authorization[0])) {
            throw new ServerException("没有token，请重新登录！", 500);
        }
        /** @var User $user */
        $user = User::query()->where('token', $authorization[0])->first();
        if (!isset($user->id)) {
            throw new ServerException("该用户账号不存在，请重新登录！", 500);
        }

        return $handler->handle($request);
    }
}