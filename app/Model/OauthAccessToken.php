<?php

declare (strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property string $id 
 * @property string $user_id
 * @property string $client_id
 * @property string $name 
 * @property string $scopes 
 * @property int $revoked 
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $expires_at 
 */
class OauthAccessToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_tokens';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'client_id', 'name', 'scopes', 'revoked', 'created_at', 'updated_at', 'expires_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['revoked' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public $incrementing = false;

    protected $keyType = 'string';
}