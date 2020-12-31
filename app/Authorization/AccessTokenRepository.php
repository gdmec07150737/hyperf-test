<?php

declare(strict_types=1);

namespace App\Authorization;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Throwable;
use App\Authorization\Entity\AccessTokenEntity;
use App\Model\OauthAccessToken;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * @param ClientEntityInterface $clientEntity
     * @param array $scopes
     * @param null $userIdentifier
     * @return AccessTokenEntity|AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);
        return $accessToken;
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $oauthAccessToken = new OauthAccessToken();
        $oauthAccessToken->id = $accessTokenEntity->getIdentifier();
        $oauthAccessToken->user_id = $accessTokenEntity->getUserIdentifier();
        $oauthAccessToken->client_id = $accessTokenEntity->getClient()->getIdentifier();
        $oauthAccessToken->scopes = $this->scopesToString($accessTokenEntity->getScopes());
        $oauthAccessToken->revoked = 0;
        $oauthAccessToken->expires_at = date(
            'Y-m-d H:i:s',
            $accessTokenEntity->getExpiryDateTime()->getTimestamp()
        );
        try {
            $oauthAccessToken->save();
        } catch (Throwable $e) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId): void
    {
        /** @var OauthAccessToken $oauthAccessToken */
        $oauthAccessToken = OauthAccessToken::query()->find($tokenId);
        $oauthAccessToken->revoked = 1;
        $oauthAccessToken->save();
    }

    /**
     * @param string $tokenId
     * @return bool
     * @throws OAuthServerException
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        /** @var OauthAccessToken $oauthAccessToken */
        $oauthAccessToken = OauthAccessToken::query()->find($tokenId);
        if (empty($oauthAccessToken->id)) {
            throw OAuthServerException::invalidRefreshToken();
        }
        return (bool)$oauthAccessToken->revoked;
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     * @return string
     */
    public function scopesToString(array $scopes): string
    {
        if (empty($scopes)) {
            return '';
        }
        return trim(array_reduce($scopes, function ($result, $item) {
            return $result . ' ' . $item->getIdentifier();
        }));
    }
}
