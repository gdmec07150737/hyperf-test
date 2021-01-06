<?php

declare(strict_types=1);

namespace App\Authorization;

use App\Model\OauthUser;
use App\Model\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use App\Authorization\Entity\ClientEntity;
use App\Model\OauthClient;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @param string|int $clientIdentifier
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
            (bool)($oauthClient->is_confidential ?? null)
        );
    }

    /**
     * @param string $clientIdentifier
     * @param string|null $clientSecret
     * @param string|null $grantType
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $oauthClient = $this->getClientData($clientIdentifier);
        if ($oauthClient === null) {
            return false;
        }
        if (!$this->isGranted($oauthClient, $grantType)) {
            return false;
        }
        if (empty($oauthClient->secret) || ($clientSecret !== $oauthClient->secret)) {
            return false;
        }
        return true;
    }

    /**
     * @param OauthClient $oauthClient
     * @param string|null $grantType
     * @return bool
     */
    protected function isGranted(OauthClient $oauthClient, string $grantType = null): bool
    {
        switch ($grantType) {
            case 'authorization_code':
                return !($oauthClient->personal_access_client || $oauthClient->password_client);
            case 'personal_access':
                return (bool)$oauthClient->personal_access_client;
            case 'password':
                return (bool)$oauthClient->password_client;
            default:
                return true;
        }
    }

    /**
     * @param int $clientIdentifier
     * @return OauthClient|null
     */
    public function getClientData($clientIdentifier): ?OauthClient
    {
        /** @var OauthClient $oauthClient */
        $oauthClient = OauthClient::query()->where('id', $clientIdentifier)->first();
        if (empty($oauthClient->id)) {
            return null;
        }
        return $oauthClient;
    }

    /**
     * @param OauthUser $user
     * @param string $name
     * @param string $redirect
     * @param int $personalAccessClient
     * @param int $password_client
     * @param int $revoked
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model
     */
    public function addClientEntity(
        OauthUser $user,
        string $name = 'Password Grant Client',
        string $redirect = 'http://localhost',
        int $personalAccessClient = 0,
        int $password_client = 1,
        int $revoked = 0
    ){
        return OauthClient::query()->create(
            [
                'user_id' => $user->id,
                'name' => $name,
                'secret' => md5($user->username . time()),
                'redirect' => $redirect,
                'personal_access_client' => $personalAccessClient,
                'password_client' => $password_client,
                'revoked' => $revoked,
            ]
        );
    }
}
