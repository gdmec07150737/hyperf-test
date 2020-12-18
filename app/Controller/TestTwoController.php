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

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Class TestTwoController.
 * @Controller(server="innerHttp")
 */
class TestTwoController extends AbstractController
{
    /**
     * @RequestMapping(path="index", methods="get,post")
     * @return string[]
     */
    public function index()
    {
        $name = $this->request->input('test', '默认值2');
        return ['msg' => 'hello2 ' . $name . '!'];
    }

    /**
     * @RequestMapping(path="test", methods="get,post")
     * @return string[]
     */
    public function test()
    {
        $name = $this->request->input('test', 'test方法的默认值');
        return ['msg' => 'TestTwoController的test方法hello ' . $name . '!'];
    }
}
