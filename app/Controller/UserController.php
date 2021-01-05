<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Throwable;
use App\Authorization\Entity\UserEntity;
use App\Authorization\SetAuthorizationServer;
use App\Exception\ServerException;
use App\Model\User;
use App\Middleware\VerifyLogin;
use App\Middleware\ValidateAccessTokensMiddleware;
use App\Request\UserDeleteRequest;
use App\Request\UserLoginRequest;
use App\Request\UserRegisteredRequest;
use App\Request\UserSelectRequest;
use App\Request\UserUpdateRequest;

/**
 * @Controller()
 */
class UserController
{
    /**
     * @Inject
     * @var SetAuthorizationServer
     */
    private $server;

    /**
     * @PostMapping(path="test-client-credentials-grant")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return Psr7ResponseInterface
     */
    public function testClientCredentialsGrant(
        RequestInterface $request,
        ResponseInterface $response
    ): ?Psr7ResponseInterface
    {
        try {
            return $this->server->getServer()->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (Throwable $e) {
            throw new ServerException($e->getMessage(), 500);
        }
    }

    /**
     * @PostMapping(path="test-access-token")
     * @Middleware(ValidateAccessTokensMiddleware::class)
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return array
     */
    public function testValidateAccessToken(RequestInterface $request, ResponseInterface $response)
    {
        var_dump($request->getAttribute('token'));
        var_dump('-------------------------');
        var_dump($request->getAttribute('user'));
        return ['code' => 200, 'msg' => '测试 access token 成功！'];
    }

    /**
     * @PostMapping(path="test-password-grant")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return Psr7ResponseInterface
     */
    public function testPasswordGrant(RequestInterface $request, ResponseInterface $response): ?Psr7ResponseInterface
    {
        try {
            return $this->server->getServer()->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (Throwable $e) {
            throw new ServerException($e->getMessage(), 500);
        }
    }

    /**
     * @PostMapping(path="test-refresh-token-grant")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return Psr7ResponseInterface
     */
    public function testRefreshTokenGrant(
        RequestInterface $request,
        ResponseInterface $response
    ): ?Psr7ResponseInterface
    {
        try {
            return $this->server->getServer()->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (Throwable $e) {
            throw new ServerException($e->getMessage(), 500);
        }
    }

    /**
     * @GetMapping(path="test-implicit-grant")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return Psr7ResponseInterface
     */
    public function testImplicitGrant(RequestInterface $request, ResponseInterface $response): ?Psr7ResponseInterface
    {
        try {
            $authRequest = $this->server->getServer()->validateAuthorizationRequest($request);
            $authRequest->setUser(new UserEntity('test'));
            $authRequest->setAuthorizationApproved(true);
            return $server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (Throwable $e) {
            throw new ServerException($e->getMessage(), 500);
        }
    }

    /**
     * @PostMapping(path="test-Authorization-code-grant")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return Psr7ResponseInterface
     */
    public function testAuthorizationCodeGrant(
        RequestInterface $request,
        ResponseInterface $response
    ): ?Psr7ResponseInterface
    {
        try {
            $authRequest = $this->server->getServer()->validateAuthorizationRequest($request);
            $authRequest->setUser(new UserEntity('user_test'));
            $authRequest->setAuthorizationApproved(true);
            return $server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (Throwable $e) {
            throw new ServerException($e->getMessage(), 500);
        }
    }

    /**
     * @GetMapping(path="/redirect")
     * @return bool
     */
    public function testRedirect(): bool
    {
        return true;
    }

    /**
     * 用户注册
     *
     * @PostMapping(path="registered")
     * @param UserRegisteredRequest $request
     * @return array
     */
    public function registered(UserRegisteredRequest $request): array
    {
        $user = new User();
        $user->email = trim($request->input('email'));
        $user->salt = uniqid('', false);
        $user->password = md5(trim($request->input('password')) . $user->salt);
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException("注册失败，请联系管理员！", 500);
        }
        return ['code' => 200, 'msg' => '注册成功！'];
    }

    /**
     * 用户退出登录
     *
     * @GetMapping(path="logout")
     * @Middleware(VerifyLogin::class)
     * @param UserDeleteRequest $request
     * @return array
     */
    public function logout(UserDeleteRequest $request): array
    {
        /** @var User $user */
        $user = $request->getAttribute('user');
        $user->token = '';
        $user->token_time_out = time();
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException("退出登录失败，请联系管理员！", 500);
        }
        return ['code' => 200, 'msg' => '退出登录成功！'];
    }

    /**
     * 用户登录
     *
     * @PostMapping(path="login")
     * @param UserLoginRequest $request
     * @return array
     */
    public function login(UserLoginRequest $request): array
    {
        /** @var User $user */
        $user = User::query()
            ->where('email', trim($request->input('email')))
            ->first();
        if (!isset($user->id) || md5(trim($request->input('password')) . $user->salt) !== $user->password) {
            throw new ServerException("用户名或密码错误！", 500);
        }
        if ($user->state !== 'normal') {
            throw new ServerException("该用户账号被禁止登录，请联系管理员！", 500);
        }
        $token = md5($user->email . time());
        $user->token = $token;
        // Token 过期时间设置为 5 分钟
        $user->token_time_out = time() + (5 * 60);
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException("更新token失败！" . $throwable->getMessage(), 500);
        }
        return ['code' => 200, 'msg' => '登录成功！', 'token' => $token, 'id' => $user->id];
    }

    /**
     * 添加用户账号
     *
     * @PostMapping(path="add_user")
     * @Middleware(VerifyLogin::class)
     * @param UserRegisteredRequest $request
     * @return array
     */
    public function addUser(UserRegisteredRequest $request): array
    {
        $user = new User();
        $user->email = trim($request->input('email'));
        $user->salt = uniqid('', false);
        $user->password = md5(trim($request->input('password')) . $user->salt);
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException("添加失败，请检查该邮箱是否已注册！", 500);
        }
        return ['code' => 200, 'msg' => '添加成功！'];
    }

    /**
     * 删除用户账号
     *
     * @DeleteMapping(path="delete_user")
     * @Middleware(VerifyLogin::class)
     * @param UserDeleteRequest $request
     * @return array
     */
    public function deleteUser(UserDeleteRequest $request): array
    {
        try {
            User::destroy(trim($request->input('id')));
        } catch (Throwable $throwable) {
            throw new ServerException('删除失败！请联系开发者', 500);
        }
        return ['code' => 200, 'msg' => '删除成功'];
    }

    /**
     * 修改用户信息
     *
     * @PostMapping(path="update_user")
     * @Middleware(VerifyLogin::class)
     * @param UserUpdateRequest $request
     * @return array
     */
    public function updateUserInfo(UserUpdateRequest $request): array
    {
        /** @var User $user */
        $user = User::query()->find(trim($request->input('id')));
        if (!isset($user->id)) {
            throw new ServerException("用户不存在，请刷新后再操作！", 500);
        }
        $user->email = trim($request->input('email'));
        $user->state = trim($request->input('state'));
        $user->password = md5(trim($request->input('password')) . $user->salt);
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException('修改失败,请检查该邮箱是否已注册！', 500);
        }
        return ['code' => 200, "msg" => '修改用户账号信息成功！'];
    }

    /**
     * 修改用户状态
     *
     * @PostMapping(path="update_user_state")
     * @Middleware(VerifyLogin::class)
     * @param UserDeleteRequest $request
     * @return array
     */
    public function updateUserState(UserDeleteRequest $request): array
    {
        /** @var User $user */
        $user = User::query()->find(trim($request->input('id')));
        if (!isset($user->id)) {
            throw new ServerException('用户不存在，请刷新后再操作！', 500);
        }
        $user->state = trim($request->input('state'));
        try {
            $user->save();
        } catch (Throwable $throwable) {
            throw new ServerException('修改用户状态失败！' . $throwable->getMessage(), 500);
        }
        return ['code' => 200, "msg" => '修改用户账号信息成功！'];
    }

    /**
     * 查询或者遍历用户账号
     *
     * @GetMapping(path="select_or_query")
     * @Middleware(VerifyLogin::class)
     * @param UserSelectRequest $request
     * @return array
     */
    public function selectOrQuery(UserSelectRequest $request): array
    {
        $columns = ['id', 'email', 'state', 'created_at'];
        $perPage = (int)trim($request->input('perPage'));
        $page = (int)trim($request->input('page'));
        $email = trim($request->input('email'));
        if (!empty($email)) {
            $userList = User::where('email', 'like', "{$email}%")
                ->paginate($perPage, $columns, 'page', $page);
        } else {
            $userList = User::paginate($perPage, $columns, 'page', $page);
        }
        return ['code' => 200, 'data' => $userList];
    }

    /**
     * 根据id查询用户信息
     *
     * @GetMapping(path="get_user")
     * @Middleware(VerifyLogin::class)
     * @param UserDeleteRequest $request
     * @return array
     */
    public function getUserById(UserDeleteRequest $request): array
    {
        /** @var User $user */
        $user = User::query()->find(trim($request->input('id')));
        if (!isset($user->id)) {
            throw new ServerException('用户不存在，请刷新后再操作！', 500);
        }
        return ['code' => 200, 'msg' => '查询用户成功!', 'data' => $user];
    }

}
