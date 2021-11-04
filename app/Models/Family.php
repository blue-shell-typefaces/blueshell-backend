<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'style_price',
        'family_price',
        'axes',
        'filename',
        'sample_text'
    ];
}
