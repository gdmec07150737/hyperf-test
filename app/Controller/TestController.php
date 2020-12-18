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

use Hyperf\HttpServer\Annotation\AutoController;

/**
 * Class TestController.
 * @AutoController(server="innerHttp")
 */
class TestController extends AbstractController
{
    public function index()
    {
        $name = $this->request->input('test', '默认值');
        return ['msg' => 'hello ' . $name . '!'];
    }

    public function test()
    {
        $name = $this->request->input('test', 'test方法的默认值');
        return ['msg' => 'TestController的test方法 hello ' . $name . '!'];
    }
}
