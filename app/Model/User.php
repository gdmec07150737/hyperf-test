<?php

declare (strict_types=1);
namespace App\Model;

use Carbon\Carbon;
/**
 * @property int $id 
 * @property string $email 邮箱
 * @property string $password 密码
 * @property string $state 用户状态（normal/正常、abnormal/异常）
 * @property string $salt 加密盐
 * @property \Carbon\Carbon $created_at 用户账号注册时间
 * @property \Carbon\Carbon $updated_at 用户账号修改时间
 * @property string $token 用户token
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'email', 'password', 'state', 'salt', 'created_at', 'updated_at', 'token'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}