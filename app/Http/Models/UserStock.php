<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserStock extends Model
{
    protected $table = 'user_stocks';

    protected $fillable = [
        'stock_id', 'user_id', 'cate1', 'cost', 'hold_num',
    ];

    /**
     * user stocks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stocks()
    {
        return $this->hasOne(Stock::class, 'id', 'stock_id');
    }
}
