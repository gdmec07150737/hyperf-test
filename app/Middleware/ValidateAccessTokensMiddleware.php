<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Model\OauthAccessToken;
use App\Model\User;
use Exception;
use Hyperf\Utils\Context;
use Lcobucci\JWT\Parser;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Authorization\AccessTokenRepository;

class ValidateAccessTokensMiddleware implements MiddlewareInterface
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
        $response = Context::get(ResponseInterface::class);
        //用来需要验证登录的功能
        $authorization = str_replace("Bearer ", "", $request->getHeader('Authorization'));
        if (!isset($authorization[0])) {
            return $response->withStatus(401)->json([
                'message' => '没有 token,请重新登录！'
            ]);
        }
        $token = (new Parser())->parse($authorization[0]);
        if (!$token->hasClaim('jti')) {
            return $response->withStatus(401)->json([
                'message' => ' Access-token 解析失败，请重新登录！'
            ]);
        }
        /** @var OauthAccessToken $oauthAccessToken */
        $oauthAccessToken = OauthAccessToken::query()->where('id', $token->getClaim('jti'))->first();
        if (empty($oauthAccessToken->id)) {
            return $response->withStatus(401)->json([
                'message' => '没有 access-token,请重新登录！'
            ]);
        }
        $accessTokenRepository = new AccessTokenRepository();
        $publicKeyPath = config('authorization.public_key');
        $server = new ResourceServer(
            $accessTokenRepository,
            $publicKeyPath
        );
        $requestBody = [
            'oauth_access_token_id' => $oauthAccessToken->id,
            'oauth_client_id' => $oauthAccessToken->client_id,
            'oauth_user_id' => $oauthAccessToken->user_id,
            'oauth_scopes' => $oauthAccessToken->scopes
        ];
        $request->getBody()->write(json_encode($requestBody));
        try {
            $request = $server->validateAuthenticatedRequest($request);
            $email = $request->getAttribute('oauth_user_id');
            $user = User::query()->where('email', $email)->first();
            $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($user, $oauthAccessToken) {
                $request = $request->withAttribute('user', $user);
                return $request->withAttribute('token', $oauthAccessToken);
            });
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
        }
        return $handler->handle($request);
    }
}