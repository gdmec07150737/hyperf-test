<?php

declare(strict_types=1);

namespace App\Authorization;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use App\Authorization\Entity\ClientEntity;
use App\Model\OauthClient;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @param string $clientIdentifier
     * @return ClientEntity|ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier)
    {
        $oauthClient = $this->getClientData($clientIdentifier);
        if ($oauthClient === null) {
            return null;
        }
        return new ClientEntity(
            $clientIdentifier,
            $oauthClient->name ?? '',
            $oauthClient->redirect ?? '',
            (bool) ($oauthClient->is_confidential ?? null)
        );
    }

    /**
     * @param string $clientIdentifier
     * @param string|null $clientSecret
     * @param string|null $grantType
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType) : bool
    {
        $oauthClient = $this->getClientData($clientIdentifier);
        if ($oauthClient === null) {
            return false;
        }
        if (! $this->isGranted($oauthClient, $grantType)) {
            return false;
        }
        if (empty($oauthClient->secret) || ! password_verify((string) $clientSecret, $oauthClient->secret)) {
            return false;
        }
        return true;
    }

    /**
     * @param OauthClient $oauthClient
     * @param string|null $grantType
     * @return bool
     */
    protected function isGranted(OauthClient $oauthClient, string $grantType = null) : bool
    {
        switch ($grantType) {
            case 'authorization_code':
                return ! ($oauthClient->personal_access_client || $oauthClient->password_client);
            case 'personal_access':
                return (bool) $oauthClient->personal_access_client;
            case 'password':
                return (bool) $oauthClient->password_client;
            default:
                return true;
        }
    }

    /**
     * @param string $clientIdentifier
     * @return OauthClient|null
     */
    public function getClientData(string $clientIdentifier): ?OauthClient
    {
        /** @var OauthClient $oauthClient */
        $oauthClient = OauthClient::query()->where('name', $clientIdentifier)->first();
        if (empty($oauthClient->id)) {
            return null;
        }
        return $oauthClient;
    }
}
