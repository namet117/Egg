<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int            $id
 * @property string         $type           类型：shares股票；etf场内基金；fund场外基金
 * @property string         $code           股票或基金编号
 * @property string         $name           股票或基金名字
 * @property string         $open           开盘价
 * @property string         $estimate       估值
 * @property string         $estimate_ratio 估值跌涨幅
 * @property string         $real           净值
 * @property string         $real_ratio     净值跌涨幅
 * @property string         $estimate_date  估值的日期
 * @property string         $real_date      净值的日期
 * @property string         $last_real      上日净值
 * @property string         $last_real_date 上日净值日期
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Stock extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stocks';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'open', 'estimate', 'real', 'estimate_date', 'real_date', 'estimate_ratio', 'real_ratio',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    const TYPE = [
        'fund' => '场外基',
        'etf' => 'ETF',
        'shares' => '股票',
    ];
}
