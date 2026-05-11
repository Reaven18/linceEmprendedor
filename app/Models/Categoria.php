<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
     use HasFactory;

    public $timestamps = false;
    protected $table = 'categorias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre'
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_categorias', 'id_categoria', 'id_producto');
    }
}
