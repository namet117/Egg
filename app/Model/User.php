<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int            $id
 * @property string         $name
 * @property string         $password
 * @property int            $custom     手动注册账号
 * @property \Carbon\Carbon $login_at   最后登录时间
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password', 'custom', 'login_at',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'custom' => 'integer',
        'login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
