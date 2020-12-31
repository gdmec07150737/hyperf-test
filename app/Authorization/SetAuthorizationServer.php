<?php

declare(strict_types=1);

namespace App\Authorization;

use DateInterval;
use Exception;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;

class SetAuthorizationServer
{
    /** @var ClientRepository */
    protected  $clientRepository;

    /** @var ScopeRepository */
    protected  $scopeRepository;

    /** @var AccessTokenRepository */
    protected  $accessTokenRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var RefreshTokenRepository */
    protected $refreshTokenRepository;

    /** @var AuthCodeRepository */
    protected $authCodeRepository;

    /** @var AuthorizationServer */
    protected $server;

    protected $privateKey = '/mnt/e/code/php-code/private-new.key';

    protected $encryptionKey = 'T2x2+1OGrElaminasS+01OUmwhOcJiGmE58UD1fllNn6CGcQ=';

    public function __construct()
    {
        $this->clientRepository = new ClientRepository();
        $this->scopeRepository = new ScopeRepository();
        $this->accessTokenRepository = new AccessTokenRepository();
        $this->userRepository = new UserRepository();
        $this->refreshTokenRepository = new RefreshTokenRepository();
        $this->authCodeRepository = new AuthCodeRepository();
        $this->server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            $this->privateKey,
            $this->encryptionKey
        );
    }

    /**
     * @return AuthorizationServer
     */
    public function clientCredentialsGrant(): AuthorizationServer
    {
        $this->server->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval('PT1H')
        );
        return $this->server;
    }

    /**
     * @return AuthorizationServer
     */
    public function passwordGrant(): AuthorizationServer
    {
        $grant = new PasswordGrant(
            $this->userRepository,
            $this->refreshTokenRepository
        );
        $this->server->enableGrantType(
            $grant,
            new DateInterval('PT1H')
        );
        return $this->server;
    }

    /**
     * @return AuthorizationServer
     */
    public function refreshTokenGrant(): AuthorizationServer
    {
        $grant = new RefreshTokenGrant($this->refreshTokenRepository);
        $grant->setRefreshTokenTTL(new DateInterval('P1M'));
        $this->server->enableGrantType(
            $grant,
            new DateInterval('PT1H')
        );
        return $this->server;
    }

    /**
     * @return AuthorizationServer
     */
    public function implicitGrant(): AuthorizationServer
    {
        $this->server->enableGrantType(
            new ImplicitGrant(new DateInterval('PT1H')),
            new DateInterval('PT1H')
        );
        return $this->server;
    }

    /**
     * @return AuthorizationServer
     * @throws Exception
     */
    public function authorizationCodeGrant(): AuthorizationServer
    {
        $grant = new AuthCodeGrant(
            $this->authCodeRepository,
            $this->refreshTokenRepository,
            new DateInterval('PT10M')
        );
        $grant->setRefreshTokenTTL(new DateInterval('P1M'));
        $this->server->enableGrantType(
            $grant,
            new DateInterval('PT1H')
        );
        return $this->server;
    }
}
