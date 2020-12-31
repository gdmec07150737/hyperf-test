<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property string $id
 */
class OauthScope extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_scopes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
