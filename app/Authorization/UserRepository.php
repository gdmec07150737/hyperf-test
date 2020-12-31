<?php

declare(strict_types=1);

namespace App\Authorization;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use App\Authorization\Entity\UserEntity;
use App\Model\OauthUser;

class UserRepository implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        /** @var OauthUser $oauthUser */
        $oauthUser = OauthUser::query()->where('username', $username)->first();
        if (empty($oauthUser->id) || empty($oauthUser->password)) {
            return null;
        }
        if (! password_verify($password, $oauthUser->password)) {
            return null;
        }
        return new UserEntity($oauthUser->username);
    }
}
