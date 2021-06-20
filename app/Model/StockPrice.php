<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int            $id
 * @property int            $stock_id
 * @property string         $day
 * @property string         $price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class StockPrice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stock_prices';
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
    protected $casts = ['id' => 'integer', 'stock_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
