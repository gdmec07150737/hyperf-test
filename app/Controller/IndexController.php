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

use App\Event\UserRegistered;
use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Utils\Parallel;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class IndexController.
 * @AutoController
 */
class IndexController extends AbstractController
{
    /**
     * @Value("app_name")
     */
    private $appName;

    /**
     * @Value("databases.default.database")
     */
    private $databasesName;

    /**
     * @Inject
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }

    public function test()
    {
        $test = $this->request->input('test', '这是测试');
        $method = $this->request->getMethod();
        return [
            'met' => $method,
            'msg' => '内容' . $test,
        ];
    }

    public function parallel()
    {
        return ['appName' => $this->appName, 'databasesName' => $this->databasesName,
            'username' => config('databases.default.username'), ];
    }

    public function register()
    {
        echo '用户注册成功！';
        $this->eventDispatcher->dispatch(new UserRegistered('用户张三'));
        return ['msg' => '张三注册成功！'];
    }
}
