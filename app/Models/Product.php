<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Aquí le damos permiso a Laravel para guardar estos campos
    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'min_stock',
        'expiration_date',
        'status',
    ];
}
