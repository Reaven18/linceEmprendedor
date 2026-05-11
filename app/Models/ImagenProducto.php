<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImagenProducto extends Model
{
    use HasFactory;

    protected $table = 'imagenes_producto';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_producto',
        'url_imagen',
        'orden'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }

    public function getUrlAttribute($value)
    {
        return asset($value);
    }
}
