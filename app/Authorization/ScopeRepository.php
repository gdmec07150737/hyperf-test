<?php

declare(strict_types=1);

namespace App\Authorization;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use App\Authorization\Entity\ScopeEntity;
use App\Model\OauthScope;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @param string $identifier
     * @return ScopeEntity|ScopeEntityInterface|null
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        /** @var OauthScope $oauthScope */
        $oauthScope = OauthScope::query()->find($identifier);
        if (empty($oauthScope->id)) {
            return null;
        }
        $scope = new ScopeEntity();
        $scope->setIdentifier($oauthScope->id);
        return $scope;
    }

    /**
     * @param array $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null $userIdentifier
     * @return array|ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        return $scopes;
    }
}
