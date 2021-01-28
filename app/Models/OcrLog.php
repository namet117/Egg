<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcrLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path', 'image_hash', 'image_url', 'response', 'driver',
    ];
}
