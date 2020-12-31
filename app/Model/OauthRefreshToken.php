<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property string $id
 * @property string $access_token_id
 * @property int $revoked
 * @property string $expires_at
 */
class OauthRefreshToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_refresh_tokens';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'access_token_id', 'revoked', 'expires_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['revoked' => 'integer'];

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
