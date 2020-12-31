<?php

declare (strict_types=1);

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $secret
 * @property string $redirect
 * @property int $personal_access_client
 * @property int $password_client
 * @property int $revoked
 * @property int $is_confidential
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class OauthClient extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_clients';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'name', 'secret', 'redirect', 'personal_access_client', 'password_client', 'revoked', 'is_confidential', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'personal_access_client' => 'integer', 'password_client' => 'integer', 'revoked' => 'integer', 'is_confidential' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
