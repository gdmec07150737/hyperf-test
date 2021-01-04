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
    protected $clientRepository;

    /** @var ScopeRepository */
    protected $scopeRepository;

    /** @var AccessTokenRepository */
    protected $accessTokenRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var RefreshTokenRepository */
    protected $refreshTokenRepository;

    /** @var AuthCodeRepository */
    protected $authCodeRepository;

    /** @var AuthorizationServer */
    protected $server;

    protected $privateKey = '';

    protected $encryptionKey = '';

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
            $this->privateKey = config('authorization.private_key'),
            $this->encryptionKey = config('authorization.encryption_key')
        );
        $serverTypeList = explode('|',config('authorization.server_type'));
        foreach ($serverTypeList as $serverType) {
            if ($serverType === 'client_credentials') {
                $this->clientCredentialsGrant();
            } else if ($serverType === 'password') {
                $this->passwordGrant();
            } else if ($serverType === 'refresh_token') {
                $this->refreshTokenGrant();
            } else if ($serverType === 'token') {
                $this->implicitGrant();
            } else if ($serverType === 'authorization_code') {
                $this->authorizationCodeGrant();
            }
        }
    }

    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return AuthorizationServer
     */
    public function clientCredentialsGrant(): AuthorizationServer
    {
        $this->server->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval(config('authorization.client_credentials_grant.access_token_ttl'))
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
            new DateInterval(config('authorization.password_grant.access_token_ttl'))
        );
        return $this->server;
    }

    /**
     * @return AuthorizationServer
     */
    public function refreshTokenGrant(): AuthorizationServer
    {
        $grant = new RefreshTokenGrant($this->refreshTokenRepository);
        $grant->setRefreshTokenTTL(
            new DateInterval(config('authorization.refresh_token_grant.refresh_token_ttl'))
        );
        $this->server->enableGrantType(
            $grant,
            new DateInterval(config('authorization.refresh_token_grant.access_token_ttl'))
        );
        return $this->server;
    }

    /**
     * @return AuthorizationServer
     */
    public function implicitGrant(): AuthorizationServer
    {
        $this->server->enableGrantType(
            new ImplicitGrant(
                new DateInterval(config('authorization.implicit_grant.implicit_grant_ttl'))
            ),
            new DateInterval(config('authorization.implicit_grant.access_token_ttl'))
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
            new DateInterval(config('authorization.auth_code_grant.auth_code_grant_ttl'))
        );
        $grant->setRefreshTokenTTL(
            new DateInterval(config('authorization.auth_code_grant.refresh_token_ttl'))
        );
        $this->server->enableGrantType(
            $grant,
            new DateInterval(config('authorization.auth_code_grant.access_token_ttl'))
        );
        return $this->server;
    }
}
