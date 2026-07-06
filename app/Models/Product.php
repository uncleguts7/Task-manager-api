<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'stock',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
