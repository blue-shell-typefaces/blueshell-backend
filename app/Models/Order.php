<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'full_family',
        'styles',
        'paid',
    ];

    protected $casts = [
        'styles' => 'json',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function getTitle()
    {
        return $this->family->name;
    }

    public function getPrice()
    {
        if ($this->full_family) {
            $price = $this->family->family_price;
        } else {
            $price = count($this->styles) * $this->family->style_price;
        }

        return $price;
    }
}
