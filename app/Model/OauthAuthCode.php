<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property string $id
 * @property int $user_id
 * @property int $client_id
 * @property string $scopes
 * @property int $revoked
 * @property string $expires_at
 */
class OauthAuthCode extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_auth_codes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'client_id', 'scopes', 'revoked', 'expires_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['user_id' => 'integer', 'client_id' => 'integer', 'revoked' => 'integer'];

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
