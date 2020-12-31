<?php

declare(strict_types=1);

namespace App\Authorization;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Throwable;
use App\Authorization\Entity\RefreshTokenEntity;
use App\Model\OauthRefreshToken;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity;
    }

    /**
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity) : void
    {
        $oauthRefreshToken = new OauthRefreshToken();
        $oauthRefreshToken->id = $refreshTokenEntity->getIdentifier();
        $oauthRefreshToken->access_token_id = $refreshTokenEntity->getAccessToken()->getIdentifier();
        $oauthRefreshToken->revoked = 0;
        $oauthRefreshToken->expires_at = date(
            'Y-m-d H:i:s',
            $refreshTokenEntity->getExpiryDateTime()->getTimestamp()
        );
        try {
            $oauthRefreshToken->save();
        } catch (Throwable $e) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId) : void
    {
        /** @var OauthRefreshToken $oauthRefreshToken */
        $oauthRefreshToken = OauthRefreshToken::query()->find($tokenId);
        $oauthRefreshToken->revoked = 1;
        $oauthRefreshToken->save();
    }

    /**
     * @param string $tokenId
     * @return bool
     */
    public function isRefreshTokenRevoked($tokenId) : bool
    {
        /** @var OauthRefreshToken $oauthRefreshToken */
        $oauthRefreshToken = OauthRefreshToken::query()->find($tokenId);
        if (empty($oauthRefreshToken->id)) {
            return false;
        }
        return (bool) $oauthRefreshToken->revoked;
    }
}
