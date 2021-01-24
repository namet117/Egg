<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'open', 'estimate', 'real', 'estimate_date', 'real_date', 'estimate_ratio', 'real_ratio'
    ];
}
