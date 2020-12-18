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
namespace App\Controller;

use App\Middleware\Auth\FooMiddleware;
use App\Middleware\BarMiddleware;
use App\Middleware\BazMiddleware;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * Class TestMiddlewareController.
 * @AutoController
 * @Middlewares({
 *     @Middleware(FooMiddleware::class),
 *     @Middleware(BarMiddleware::class),
 *     @Middleware(BazMiddleware::class),
 * })
 */
class TestMiddlewareController
{
    public function index(RequestInterface $request2)
    {
        var_dump('request2：' . $request2->getAttribute('testName'));
        return 'testMiddleware';
    }
}
