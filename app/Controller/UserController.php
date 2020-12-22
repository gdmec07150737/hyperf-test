<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServerException;
use App\Model\User;
use App\Request\UserDeleteRequest;
use App\Request\UserLoginRequest;
use App\Request\UserRegisteredRequest;
use App\Request\UserSelectRequest;
use App\Request\UserUpdateRequest;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Throwable;
use function _HumbugBoxfb21822734fc\React\Promise\Stream\first;

/**
 * @Controller()
 */
class UserController
{

    /**
     * @Inject()
     * @var SessionInterface
     */
    private $session;

    /**
     * 用户注册
     * @PostMapping(path="registered")
     * @param UserRegisteredRequest $request
     * @return array
     */
    public function Registered(UserRegisteredRequest $request): array
    {
        $User = new User();
        $User->email = $request->validated()['email'];
        $User->salt = uniqid('', false);
        $User->password = md5($request->validated()['password'] . $User->salt);
        try {
            $User->save();
        } catch (Throwable $throwable) {
            throw new ServerException("注册失败，请联系管理员！", 500);
        }
        return ['code' => 200, 'msg' => '注册成功！'];
    }

    /**
     * 用户退出登录
     * @GetMapping(path="logout")
     * @param UserDeleteRequest $request
     * @return array
     */
    public function Logout(UserDeleteRequest $request): array
    {
        /** @var User $uer */
        $uer = User::query()->where('id' , $request->validated()['id'])->first();
        $uer->token = '';
        try {
            $uer->save();
        } catch (Throwable $throwable) {
            throw new ServerException("退出登录失败，请联系管理员！", 500);
        }
        return ['code' => 200, 'msg' => '退出登录成功！'];
    }

    /**
     * 用户登录
     * @PostMapping(path="login")
     * @param UserLoginRequest $request
     * @return array
     */
    public function Login(UserLoginRequest $request): array
    {
        /** @var User $user */
        $user = User::query()
            ->where('email', $request->validated()['email'])
            ->first();
        if (!$user || md5($request->validated()['password'] . $user->salt) !== $user->password) {
            $this->session->remove('userInfo');
            throw new ServerException("用户名或密码错误！", 500);
        }
        if ($user->state !== 'normal') {
            $this->session->remove('userInfo');
            throw new ServerException("该用户账号被禁止登录，请联系管理员！", 500);
        }
        $token = md5($user->email . time());
        $user->token = $token;
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException("更新token失败！".$throwable->getMessage(), 500);
        }
        return ['code' => 200, 'msg' => '登录成功！', 'token' => $token, 'id' => $user->id];
    }

    /**
     * 测试获取session
     * @GetMapping(path="test_get_session")
     */
    public function TestGetSession(): string
    {
        return $this->session->get('userInfo', 'not have session');
    }

    /**
     * 添加用户账号
     * @PostMapping(path="add_user")
     * @param UserRegisteredRequest $request
     * @return array
     */
    public function AddUser(UserRegisteredRequest $request): array
    {
        $User = new User();
        $User->email = $request->validated()['email'];
        $User->salt = uniqid('', false);
        $User->password = md5($request->validated()['password'] . $User->salt);
        try {
            $User->save();
        } catch (Throwable $throwable) {
            throw new ServerException("添加失败，请检查该邮箱是否已注册！", 500);
        }
        return ['code' => 200, 'msg' => '添加成功！'];
    }

    /**
     * 删除用户账号
     * @DeleteMapping(path="delete_user")
     * @param UserDeleteRequest $request
     * @return array
     */
    public function DeleteUser(UserDeleteRequest $request): array
    {
        try {
            User::destroy($request->validated()['id']);
        } catch (Throwable $throwable) {
            throw new ServerException('删除失败！请联系开发者', 500);
        }
        return ['code' => 200, 'msg' => '删除成功'];
    }

    /**
     * 修改用户信息
     * @PostMapping(path="update_user")
     * @param UserUpdateRequest $request
     * @return array
     */
    public function UpdateUserInfo(UserUpdateRequest $request): array
    {
        /** @var User $user */
        $user = User::query()->find($request->validated()['id']);
        if (!$user) {
            throw new ServerException("用户不存在，请刷新后再操作！", 500);
        }
        $user->email = $request->validated()['email'];
        $user->password = md5($request->validated()['password'] . $user->salt);
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException('修改失败,请检查该邮箱是否已注册！', 500);
        }
        return ['code' => 200, "msg" => '修改用户账号信息成功！'];
    }

    /**
     * 修改用户状态
     * @PostMapping(path="update_user_state")
     * @param UserDeleteRequest $request
     * @return array
     */
    public function UpdateUserState(UserDeleteRequest $request)
    {
        /** @var User $User */
        $User = User::query()->find($request->validated()['id']);
        if (!$User) {
            throw new ServerException('用户不存在，请刷新后再操作！', 500);
        }
        $User->state = $request->validated()['state'];
        try {
            $User->save();
        } catch (Throwable $throwable) {
            throw new ServerException('修改用户状态失败！' . $throwable->getMessage(), 500);
        }
        return ['code' => 200, "msg" => '修改用户账号信息成功！'];
    }

    /**
     * 查询或者遍历用户账号
     * @GetMapping(path="select_or_query")
     * @param UserSelectRequest $request
     * @return array
     */
    public function SelectOrQuery(UserSelectRequest $request): array
    {
        $columns = ['id','email','state','created_at'];
        $perPage = (int)$request->validated()['perPage'];
        $page = (int)$request->validated()['page'];
        if (isset($request->validated()['email'])) {
            $userList = User::where('email', 'like' ,"{$request->validated()['email']}%")
                ->paginate($perPage, $columns, 'page', $page);
        } else {
            $userList = User::paginate($perPage, $columns, 'page', $page);
        }
        return ['code' => 200, 'data' => $userList];
    }

}