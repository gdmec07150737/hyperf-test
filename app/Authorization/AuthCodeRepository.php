<?php

declare(strict_types=1);

namespace App\Authorization;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Throwable;
use App\Authorization\Entity\AuthCodeEntity;
use App\Model\OauthAuthCode;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * @return AuthCodeEntity|AuthCodeEntityInterface
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity;
    }

    /**
     * @param AuthCodeEntityInterface $authCodeEntity
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $oauthAuthCode = new OauthAuthCode();
        $oauthAuthCode->id = $authCodeEntity->getIdentifier();
        $oauthAuthCode->user_id = $authCodeEntity->getUserIdentifier();
        $oauthAuthCode->client_id = $authCodeEntity->getClient()->getIdentifier();
        $oauthAuthCode->scopes = $this->scopesToString($authCodeEntity->getScopes());
        $oauthAuthCode->revoked = 0;
        $oauthAuthCode->expires_at = date(
            'Y-m-d H:i:s',
            $authCodeEntity->getExpiryDateTime()->getTimestamp()
        );
        try {
            $oauthAuthCode->save();
        } catch (Throwable $e) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

    }

    /**
     * @param string $codeId
     */
    public function revokeAuthCode($codeId): void
    {
        /** @var OauthAuthCode $oauthAuthCode */
        $oauthAuthCode = OauthAuthCode::query()->find($codeId);
        $oauthAuthCode->revoked = 1;
        $oauthAuthCode->save();
    }

    /**
     * @param string $codeId
     * @return bool
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        /** @var OauthAuthCode $oauthAuthCode */
        $oauthAuthCode = OauthAuthCode::query()->find($codeId);
        if (empty($oauthAuthCode->id)) {
            return false;
        }
        return (bool)$oauthAuthCode->revoked;
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
