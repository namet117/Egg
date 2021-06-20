<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int            $id
 * @property int            $user_id
 * @property string         $openid
 * @property string         $unionid
 * @property string         $session_key
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserOauth extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_oauth';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
