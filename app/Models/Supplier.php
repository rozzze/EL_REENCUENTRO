<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // Le damos permiso para guardar estos datos del proveedor
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];
}
