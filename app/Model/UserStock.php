<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int            $id
 * @property int            $stock_id   股票或基金ID
 * @property int            $user_id    用户ID
 * @property string         $cate1      板块
 * @property string         $cost       成本
 * @property string         $hold_num   持有份数
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserStock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_stocks';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock_id', 'user_id', 'cate1', 'cost', 'hold_num',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'stock_id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function stocks()
    {
        return $this->hasOne(Stock::class, 'id', 'stock_id');
    }
}
